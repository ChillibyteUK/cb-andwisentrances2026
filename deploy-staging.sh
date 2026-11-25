#!/usr/bin/env bash
#
# Copy this theme to the staging site over SSH. Theme files only: it never
# touches uploads, plugins or the database, and it never deletes anything on
# the server (no --delete), so files that exist only on staging are left alone.
#
# Connection details live in .deploy-staging.env (git-ignored), not here:
#
#   DEPLOY_HOST=1.2.3.4
#   DEPLOY_PORT=12345
#   DEPLOY_USER=sitename
#   DEPLOY_PATH=/www/sitename_123/public/wp-content/themes/cb-andwisentrances2026
#   DEPLOY_KEY=~/.ssh/kinsta_deploy_key        # optional
#
# Usage:
#   ./deploy-staging.sh            list what would change, then ask before copying
#   ./deploy-staging.sh --dry-run  list what would change and stop
#   ./deploy-staging.sh --yes      copy without asking
#
set -euo pipefail

cd "$(dirname "$0")"

if [[ ! -f .deploy-staging.env ]]; then
	echo "Missing .deploy-staging.env (see the comment at the top of this script)." >&2
	exit 1
fi

# shellcheck disable=SC1091
source .deploy-staging.env

: "${DEPLOY_HOST:?}" "${DEPLOY_PORT:?}" "${DEPLOY_USER:?}" "${DEPLOY_PATH:?}"

case "$DEPLOY_PATH" in
	*/wp-content/themes/?*) ;;
	*) echo "DEPLOY_PATH must be a folder inside wp-content/themes." >&2; exit 1 ;;
esac

ssh_cmd="ssh -p $DEPLOY_PORT -o BatchMode=yes -o ConnectTimeout=20"
if [[ -n "${DEPLOY_KEY:-}" ]]; then
	ssh_cmd="$ssh_cmd -o IdentitiesOnly=yes -i ${DEPLOY_KEY/#\~/$HOME}"
fi

# -c compares file contents, so only files that really differ are listed/sent.
sync() {
	rsync -rltzc "$@" \
		--exclude '.git/' \
		--exclude 'node_modules/' \
		--exclude '.DS_Store' \
		--exclude '.deploy-staging.env' \
		--exclude 'deploy-staging.sh' \
		-e "$ssh_cmd" \
		./ "$DEPLOY_USER@$DEPLOY_HOST:${DEPLOY_PATH%/}/"
}

mode="${1:-}"

echo "Files that differ from staging:"
changes="$(sync -n -v | grep -v -E '^(sending|sent |total |Transfer|building|$)|/$' || true)"

if [[ -z "$changes" ]]; then
	echo "  (none - staging already matches)"
	exit 0
fi

echo "$changes" | sed 's/^/  /'

if [[ "$mode" == "--dry-run" ]]; then
	exit 0
fi

if [[ "$mode" != "--yes" ]]; then
	read -r -p "Copy these to staging? [y/N] " answer
	[[ "$answer" == "y" || "$answer" == "Y" ]] || { echo "Nothing copied."; exit 0; }
fi

sync >/dev/null
echo "Copied to $DEPLOY_HOST."
