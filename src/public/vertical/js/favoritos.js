/**
 * Lista de favoritos — estado persistido em localStorage.
 * Site não é SPA, então o estado precisa sobreviver a navegações entre páginas.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'vertical_favoritos';
    var AUTH = {
        loggedIn: document.body.dataset.loggedIn === '1',
        justLoggedIn: document.body.dataset.justLoggedIn === '1',
    };

    function isNumericId(id) {
        return /^\d+$/.test(String(id));
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function syncToggle(product) {
        if (!AUTH.loggedIn || !isNumericId(product.id)) return;
        fetch('/favoritos/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ id: product.id }),
        }).catch(function () {});
    }

    /**
     * Na primeira página após login, manda os favoritos do localStorage pra
     * mesclar com os do servidor; nas demais páginas, só busca do servidor
     * (fonte da verdade), sem reenviar nada.
     */
    function syncFromServer() {
        if (!AUTH.loggedIn) return;

        if (AUTH.justLoggedIn) {
            var items = loadFavoritos().filter(function (it) { return isNumericId(it.id); });
            fetch('/favoritos/mesclar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body: JSON.stringify({ items: items }),
            }).then(function (r) { return r.json(); }).then(function (serverItems) {
                saveFavoritos(serverItems);
                atualizarCoracoes();
                render();
            }).catch(function () {});
        } else {
            fetch('/favoritos/sincronizar', {
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            }).then(function (r) { return r.json(); }).then(function (serverItems) {
                saveFavoritos(serverItems);
                atualizarCoracoes();
                render();
            }).catch(function () {});
        }
    }

    function loadFavoritos() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveFavoritos(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    }

    function isFavorito(id) {
        return loadFavoritos().some(function (it) { return it.id === id; });
    }

    function toggleFavorito(product) {
        var items = loadFavoritos();
        var idx = items.findIndex(function (it) { return it.id === product.id; });
        var favoritado;
        if (idx >= 0) {
            items.splice(idx, 1);
            favoritado = false;
        } else {
            items.push({
                id: product.id,
                nome: product.nome,
                preco: product.preco,
                imagem: product.imagem,
            });
            favoritado = true;
        }
        saveFavoritos(items);
        atualizarCoracoes();
        syncToggle(product);
        return favoritado;
    }

    function removerFavorito(id) {
        var items = loadFavoritos().filter(function (it) { return it.id !== id; });
        saveFavoritos(items);
        atualizarCoracoes();
        render();
    }

    function formatBRL(v) {
        return 'R$ ' + v.toFixed(2).replace('.', ',');
    }

    function slugify(str) {
        return str
            .toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }

    /**
     * Extrai id/nome/preço/imagem de um card de produto — mesma lógica dual-mode do cart.js:
     * container explícito com [data-product-id], ou leitura direto do DOM como fallback.
     */
    function readProductFromCard(el) {
        var explicit = el.closest('[data-product-id]');
        if (explicit) {
            return {
                id: explicit.getAttribute('data-product-id'),
                nome: explicit.getAttribute('data-product-nome'),
                preco: parseFloat(explicit.getAttribute('data-product-preco')) || 0,
                imagem: explicit.getAttribute('data-product-imagem'),
            };
        }

        var card = el.closest('.item, .related-item');
        if (!card) return null;

        var img = card.querySelector('.item-img img, img');
        var nomeEl = card.querySelector('.item-sub h5, h5');
        var precoEl = card.querySelector('.item-sub p, .preco');
        if (!img || !nomeEl || !precoEl) return null;

        var nome = nomeEl.textContent.trim();
        var precoTexto = precoEl.childNodes[0] ? precoEl.childNodes[0].textContent : precoEl.textContent;
        var preco = parseFloat(
            precoTexto.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')
        ) || 0;

        return {
            id: slugify(nome),
            nome: nome,
            preco: preco,
            imagem: img.getAttribute('src'),
        };
    }

    /** Atualiza visualmente todos os corações da página + o contador no header */
    function atualizarCoracoes() {
        var favIds = loadFavoritos().map(function (it) { return it.id; });

        document.querySelectorAll('.tr_heart').forEach(function (heart) {
            var product = readProductFromCard(heart);
            if (!product || !product.id) return;
            var favoritado = favIds.indexOf(product.id) !== -1;
            heart.classList.toggle('fa-heart', favoritado);
            heart.classList.toggle('fa-heart-o', !favoritado);
            heart.classList.toggle('is-favorito', favoritado);
        });

        // botão principal da página de produto (produto.blade.php)
        var btnFav = document.querySelector('.btn-fav-main[data-product-id]');
        if (btnFav) {
            var icon = btnFav.querySelector('i');
            var favoritado = favIds.indexOf(btnFav.getAttribute('data-product-id')) !== -1;
            if (icon) {
                icon.classList.toggle('fa-heart', favoritado);
                icon.classList.toggle('fa-heart-o', !favoritado);
            }
            btnFav.classList.toggle('is-favorito', favoritado);
            btnFav.title = favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos';
        }

        var countEls = document.querySelectorAll('.js-favoritos-count');
        countEls.forEach(function (el) {
            el.textContent = favIds.length;
            el.classList.toggle('is-empty', favIds.length === 0);
        });
    }

    /** Renderiza o grid da página /favoritos, se estiver presente */
    function render() {
        var itemsEl = document.getElementById('favoritos-grid');
        if (!itemsEl) return;

        var emptyEl = document.getElementById('favoritos-empty');
        var items = loadFavoritos();

        if (items.length === 0) {
            itemsEl.innerHTML = '';
            itemsEl.style.display = 'none';
            if (emptyEl) emptyEl.style.display = 'flex';
            return;
        }

        if (emptyEl) emptyEl.style.display = 'none';
        itemsEl.style.display = '';

        itemsEl.innerHTML = items.map(function (item) {
            return (
                '<div class="col-md-3 col-sm-4 col-xs-12">' +
                    '<div class="main_cat_item">' +
                        '<div class="item" data-product-id="' + item.id + '" data-product-nome="' + item.nome + '" ' +
                             'data-product-preco="' + item.preco + '" data-product-imagem="' + item.imagem + '">' +
                            '<div class="item-img">' +
                                '<img src="' + item.imagem + '" alt="" />' +
                                '<div class="tr-add-cart">' +
                                    '<ul>' +
                                        '<li><a class="fa fa-shopping-cart tr_cart" href="#" data-add-to-cart></a></li>' +
                                        '<li><a class="tr_text" href="#" data-add-to-cart>ADICIONAR AO CARRINHO</a></li>' +
                                        '<li><a class="fa fa-heart tr_heart is-favorito" href="#"></a></li>' +
                                    '</ul>' +
                                '</div>' +
                            '</div>' +
                            '<div class="item-sub">' +
                                '<h5>' + item.nome + '</h5>' +
                                '<p>' + formatBRL(item.preco) + '</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', function () {
        atualizarCoracoes();
        render();
        syncFromServer();

        document.body.addEventListener('click', function (e) {
            var heart = e.target.closest('.tr_heart');
            if (heart) {
                e.preventDefault();
                var product = readProductFromCard(heart);
                if (product && product.id) {
                    toggleFavorito(product);
                    render();
                }
                return;
            }

            var btnFav = e.target.closest('.btn-fav-main[data-product-id]');
            if (btnFav) {
                e.preventDefault();
                toggleFavorito({
                    id: btnFav.getAttribute('data-product-id'),
                    nome: btnFav.getAttribute('data-product-nome'),
                    preco: parseFloat(btnFav.getAttribute('data-product-preco')) || 0,
                    imagem: btnFav.getAttribute('data-product-imagem'),
                });
                return;
            }
        });
    });

    window.VerticalFavoritos = {
        load: loadFavoritos,
        isFavorito: isFavorito,
        toggle: toggleFavorito,
        remove: removerFavorito,
        render: render,
    };
})();
