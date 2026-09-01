/**
 * Carrinho lateral (drawer) — estado persistido em localStorage.
 * Site não é SPA, então o estado precisa sobreviver a navegações entre páginas.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'vertical_cart';
    var FREE_SHIPPING_THRESHOLD = 150;
    var SIZES = ['PP', 'P', 'M', 'G', 'GG', 'XGG'];
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

    /** Envia pro servidor o item com a quantidade final (upsert, não incremento). */
    function syncUpsert(item) {
        if (!AUTH.loggedIn || !isNumericId(item.id)) return;
        fetch('/carrinho/item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ id: item.id, cor: item.cor || '', tamanho: item.tamanho || '', qty: item.qty }),
        }).catch(function () {});
    }

    function syncRemove(item) {
        if (!AUTH.loggedIn || !isNumericId(item.id)) return;
        fetch('/carrinho/item', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ id: item.id, cor: item.cor || '', tamanho: item.tamanho || '' }),
        }).catch(function () {});
    }

    /**
     * Ao carregar a página logado: na primeira página após o login, manda o
     * carrinho do localStorage pra mesclar com o do servidor (soma quantidades);
     * nas demais páginas, só busca o carrinho do servidor (fonte da verdade),
     * sem reenviar nada — evita duplicar quantidade a cada navegação.
     */
    function syncFromServer() {
        if (!AUTH.loggedIn) return;

        if (AUTH.justLoggedIn) {
            var items = loadCart().filter(function (it) { return isNumericId(it.id); });
            fetch('/carrinho/mesclar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body: JSON.stringify({ items: items }),
            }).then(function (r) { return r.json(); }).then(function (serverItems) {
                saveCart(serverItems);
                render();
            }).catch(function () {});
        } else {
            fetch('/carrinho/sincronizar', {
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            }).then(function (r) { return r.json(); }).then(function (serverItems) {
                saveCart(serverItems);
                render();
            }).catch(function () {});
        }
    }

    function loadCart() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    /**
     * Sempre grava ids como string: o servidor devolve id numerico
     * (product_id do banco), mas o clique no card le data-product-id
     * do DOM, que e sempre string. Sem essa normalizacao, "23" !== 23
     * faz findItemIndex nunca achar o item existente e duplicar a
     * linha em vez de somar a quantidade.
     */
    function saveCart(items) {
        var normalized = items.map(function (it) {
            return Object.assign({}, it, { id: String(it.id) });
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(normalized));
    }

    function formatBRL(v) {
        return 'R$ ' + v.toFixed(2).replace('.', ',');
    }

    function findItemIndex(items, id, cor, tamanho) {
        for (var i = 0; i < items.length; i++) {
            if (items[i].id === id && items[i].cor === cor && items[i].tamanho === tamanho) {
                return i;
            }
        }
        return -1;
    }

    function addItem(product) {
        var items = loadCart();
        var idx = findItemIndex(items, product.id, product.cor || '', product.tamanho || '');
        var finalItem;
        if (idx >= 0) {
            items[idx].qty += product.qty || 1;
            finalItem = items[idx];
        } else {
            finalItem = {
                id: product.id,
                nome: product.nome,
                preco: product.preco,
                imagem: product.imagem,
                cor: product.cor || '',
                tamanho: product.tamanho || '',
                qty: product.qty || 1,
            };
            items.push(finalItem);
        }
        saveCart(items);
        render();
        open();
        syncUpsert(finalItem);
    }

    function clearCart() {
        saveCart([]);
        render();
    }

    function removeItem(index) {
        var items = loadCart();
        var removed = items[index];
        items.splice(index, 1);
        saveCart(items);
        render();
        if (removed) syncRemove(removed);
    }

    function updateQty(index, qty) {
        var items = loadCart();
        if (!items[index]) return;
        qty = Math.max(1, qty);
        items[index].qty = qty;
        saveCart(items);
        render();
        syncUpsert(items[index]);
    }

    function cartCount(items) {
        return items.reduce(function (sum, it) { return sum + it.qty; }, 0);
    }

    function cartSubtotal(items) {
        return items.reduce(function (sum, it) { return sum + it.preco * it.qty; }, 0);
    }

    function open() {
        document.getElementById('cart-drawer').classList.add('is-open');
        document.getElementById('cart-drawer-overlay').classList.add('is-open');
        document.getElementById('cart-drawer').setAttribute('aria-hidden', 'false');
        document.body.classList.add('cart-drawer-locked');
    }

    function close() {
        document.getElementById('cart-drawer').classList.remove('is-open');
        document.getElementById('cart-drawer-overlay').classList.remove('is-open');
        document.getElementById('cart-drawer').setAttribute('aria-hidden', 'true');
        document.body.classList.remove('cart-drawer-locked');
    }

    function render() {
        var items = loadCart();
        var itemsEl = document.getElementById('cart-drawer-items');
        var emptyEl = document.getElementById('cart-drawer-empty');
        var footerEl = document.getElementById('cart-drawer-footer');
        if (!itemsEl) return;

        // contador no header
        var countEls = document.querySelectorAll('.js-cart-count');
        var count = cartCount(items);
        countEls.forEach(function (el) { el.textContent = count; });

        if (items.length === 0) {
            itemsEl.innerHTML = '';
            emptyEl.style.display = 'flex';
            footerEl.style.display = 'none';
        } else {
            emptyEl.style.display = 'none';
            footerEl.style.display = 'block';
            itemsEl.innerHTML = items.map(function (item, i) {
                var meta = [];
                if (item.tamanho) meta.push('Tam: ' + item.tamanho);
                if (item.cor) meta.push('Cor: ' + item.cor);
                return (
                    '<div class="cart-drawer-item" data-index="' + i + '">' +
                        '<img src="' + item.imagem + '" alt="" class="cart-drawer-item-img" />' +
                        '<div class="cart-drawer-item-info">' +
                            '<p class="cart-drawer-item-name">' + item.nome + '</p>' +
                            (meta.length ? '<p class="cart-drawer-item-meta">' + meta.join(' · ') + '</p>' : '') +
                            '<div class="cart-drawer-item-qty">' +
                                '<button type="button" class="cart-drawer-qty-btn" data-action="dec" data-index="' + i + '">−</button>' +
                                '<span>' + item.qty + '</span>' +
                                '<button type="button" class="cart-drawer-qty-btn" data-action="inc" data-index="' + i + '">+</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="cart-drawer-item-right">' +
                            '<button type="button" class="cart-drawer-item-remove" data-index="' + i + '" title="Remover"><i class="fa fa-trash-o"></i></button>' +
                            '<span class="cart-drawer-item-price">' + formatBRL(item.preco * item.qty) + '</span>' +
                        '</div>' +
                    '</div>'
                );
            }).join('');
        }

        var subtotal = cartSubtotal(items);
        var subtotalEl = document.getElementById('cart-drawer-subtotal');
        if (subtotalEl) subtotalEl.textContent = formatBRL(subtotal);

        // barra de frete grátis
        var msgEl = document.getElementById('cart-drawer-shipping-msg');
        var fillEl = document.getElementById('cart-drawer-shipping-fill');
        if (msgEl && fillEl) {
            if (subtotal >= FREE_SHIPPING_THRESHOLD) {
                msgEl.innerHTML = '<i class="fa fa-check"></i> Você ganhou frete grátis';
                msgEl.classList.add('is-complete');
                fillEl.style.width = '100%';
            } else {
                var falta = FREE_SHIPPING_THRESHOLD - subtotal;
                msgEl.innerHTML = 'Faltam <strong>' + formatBRL(falta) + '</strong> para frete grátis';
                msgEl.classList.remove('is-complete');
                fillEl.style.width = Math.min(100, (subtotal / FREE_SHIPPING_THRESHOLD) * 100) + '%';
            }
        }

        // sincroniza a página de carrinho completa, se estiver aberta
        if (typeof window.onCartDrawerRender === 'function') {
            window.onCartDrawerRender(items);
        }
    }

    function slugify(str) {
        return str
            .toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }

    /**
     * Extrai id/nome/preço/imagem de um gatilho "adicionar ao carrinho".
     * Suporta dois formatos:
     *  1) Container explícito com [data-product-id] + data-product-nome/preco/imagem
     *     (usado no botão principal de produto.blade.php e nos "relacionados").
     *  2) Cards de listagem (.item / .related-item, ex.: tendencias.blade.php)
     *     sem atributos — os dados são lidos direto do DOM (imagem, h5, preço),
     *     e o id é derivado (slug) do nome, já que o site não tem IDs reais de produto.
     */
    function readProductFromTrigger(el) {
        var explicit = el.closest('[data-product-id]');
        if (explicit) {
            return {
                id: explicit.getAttribute('data-product-id'),
                nome: explicit.getAttribute('data-product-nome'),
                preco: parseFloat(explicit.getAttribute('data-product-preco')) || 0,
                imagem: explicit.getAttribute('data-product-imagem'),
                cor: explicit.getAttribute('data-product-cor') || '',
                tamanho: explicit.getAttribute('data-product-tamanho') || '',
                qty: parseInt(explicit.getAttribute('data-product-qty'), 10) || 1,
            };
        }

        var card = el.closest('.item, .related-item');
        if (!card) return null;

        var img = card.querySelector('.item-img img, img');
        var nomeEl = card.querySelector('.item-sub h5, h5');
        var precoEl = card.querySelector('.item-sub p, .preco');
        if (!img || !nomeEl || !precoEl) return null;

        var nome = nomeEl.textContent.trim();
        // primeiro nó de texto do <p> — ignora <del>/<span> de preço "de"
        var precoTexto = precoEl.childNodes[0] ? precoEl.childNodes[0].textContent : precoEl.textContent;
        var preco = parseFloat(
            precoTexto.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')
        ) || 0;

        return {
            id: slugify(nome),
            nome: nome,
            preco: preco,
            imagem: img.getAttribute('src'),
            qty: 1,
        };
    }

    function closeSizePicker() {
        var open = document.querySelector('.size-picker-popover');
        if (open) open.remove();
    }

    /**
     * Abre um popover de seleção de tamanho ancorado no card do produto
     * (dentro de .item-img, que já é position:relative). Usado sempre que
     * "adicionar ao carrinho" é clicado a partir de uma listagem — o produto
     * só é adicionado depois que um tamanho é escolhido.
     */
    function showSizePicker(trigger, product) {
        closeSizePicker();

        var anchor = trigger.closest('.item-img');
        if (!anchor) {
            // sem card pra ancorar (ex.: botão principal da página de produto) — adiciona direto
            addItem(product);
            return;
        }

        var popover = document.createElement('div');
        popover.className = 'size-picker-popover';
        popover.innerHTML =
            '<button type="button" class="size-picker-close" aria-label="Fechar">&times;</button>' +
            '<p class="size-picker-title">Escolha o tamanho</p>' +
            '<div class="size-picker-grid">' +
            SIZES.map(function (s) {
                return '<button type="button" class="size-picker-btn" data-size="' + s + '">' + s + '</button>';
            }).join('') +
            '</div>';
        popover.__product = product;

        anchor.appendChild(popover);
    }

    document.addEventListener('DOMContentLoaded', function () {
        render();
        syncFromServer();

        document.getElementById('cart-drawer-close').addEventListener('click', close);
        document.getElementById('cart-drawer-overlay').addEventListener('click', close);
        document.getElementById('cart-drawer-continue').addEventListener('click', close);

        // delega clique em qualquer elemento com [data-add-to-cart] ou que abra/feche o drawer
        document.body.addEventListener('click', function (e) {
            var sizeBtn = e.target.closest('.size-picker-btn');
            if (sizeBtn) {
                e.preventDefault();
                var popover = sizeBtn.closest('.size-picker-popover');
                var product = popover.__product;
                product.tamanho = sizeBtn.getAttribute('data-size');
                addItem(product);
                popover.remove();
                return;
            }

            var closePickerBtn = e.target.closest('.size-picker-close');
            if (closePickerBtn) {
                e.preventDefault();
                closeSizePicker();
                return;
            }

            var openTrigger = e.target.closest('.js-cart-open');
            if (openTrigger) {
                e.preventDefault();
                var mobiMenu = document.querySelector('.mobi-menu');
                if (mobiMenu) mobiMenu.style.display = 'none';
                open();
                return;
            }

            var continueEmptyTrigger = e.target.closest('.js-cart-open-continue');
            if (continueEmptyTrigger) {
                e.preventDefault();
                close();
                return;
            }

            var trigger = e.target.closest('[data-add-to-cart]');
            if (trigger) {
                e.preventDefault();
                var newProduct = readProductFromTrigger(trigger);
                if (newProduct && newProduct.id) {
                    showSizePicker(trigger, newProduct);
                }
                return;
            }

            // clique fora de um popover aberto fecha ele
            var openPopover = document.querySelector('.size-picker-popover');
            if (openPopover && !e.target.closest('.size-picker-popover')) {
                openPopover.remove();
            }

            var qtyBtn = e.target.closest('.cart-drawer-qty-btn');
            if (qtyBtn) {
                var idx = parseInt(qtyBtn.getAttribute('data-index'), 10);
                var items = loadCart();
                var current = items[idx] ? items[idx].qty : 1;
                var delta = qtyBtn.getAttribute('data-action') === 'inc' ? 1 : -1;
                updateQty(idx, current + delta);
                return;
            }

            var removeBtn = e.target.closest('.cart-drawer-item-remove');
            if (removeBtn) {
                var rIdx = parseInt(removeBtn.getAttribute('data-index'), 10);
                removeItem(rIdx);
                return;
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSizePicker();
                close();
            }
        });
    });

    // API pública, usada por produto.blade.php e carrinho.blade.php
    window.VerticalCart = {
        load: loadCart,
        save: saveCart,
        add: addItem,
        remove: removeItem,
        clear: clearCart,
        updateQty: updateQty,
        open: open,
        close: close,
        render: render,
        formatBRL: formatBRL,
        count: cartCount,
        subtotal: cartSubtotal,
    };
})();
