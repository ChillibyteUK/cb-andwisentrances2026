# cb-andwisentrances2026

WordPress theme for andwis entrances (Etron, rebranded), built on
`cb-js-skeleton2026` (native blocks, no ACF) and styled to sit alongside
andwis lifts (`cb-andwislifts2026`): same forest/sage/mint palette, Poppins,
pill buttons and the curved "wave" section edges.

See `CLAUDE.md` for the skeleton's architecture rules and the project notes
at the bottom of that file for everything specific to this site (content
model, blocks, settings, build).

## Build

```
npm install
npm run watch        # theme CSS/JS + block JS watchers
npm run dist         # one-off: css + js + blocks:build
```

Compiled `css/`, `js/` and each block's `build/` are committed.

## Local

DevKinsta site `andwisentrancescom.local`. WP-CLI runs inside the FPM
container, e.g.

```
docker exec -w /www/kinsta/public/andwisentrancescom devkinsta_fpm wp --allow-root plugin list
```
