#!/usr/bin/env bash
# Fails if any Blade view loads a resource (link href, script src, css url())
# over plain http:// — those get blocked as mixed content once the site is
# served over https, silently breaking icons/scripts/backgrounds in prod.
# XML/RDF namespace declarations (xmlns="http://www.w3.org/..." etc.) are not
# loaded resources and are excluded.
set -euo pipefail

cd "$(dirname "$0")/.."

MATCHES=$(grep -rnE 'href="http://|src="http://|url\(.?http://' resources/views \
  | grep -v -e 'http://schema.org' -e 'http://www.w3.org' || true)

if [ -n "$MATCHES" ]; then
  echo "Mixed-content check failed: found http:// resource references in resources/views:" >&2
  echo "$MATCHES" >&2
  echo "" >&2
  echo "Switch these to https:// (or host the asset locally) before deploying." >&2
  exit 1
fi

echo "Mixed-content check passed: no http:// resource references found in resources/views."
