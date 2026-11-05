#!/bin/bash
W() { docker exec -w /www/kinsta/public/andwisentrancescom devkinsta_fpm wp --allow-root "$@"; }
set_summary() { local id; id=$(W post list --post_type="$1" --name="$2" --post_parent="${4:-0}" --fields=ID --format=csv | tail -n +2 | head -1); [ -n "$id" ] && W post meta update "$id" card_summary "$3" >/dev/null && echo "$1/$2 -> $id"; }
set_summary product automatic-revolving-doors "Bi-folding, sliding, swing and revolving doors for busy, people-facing entrances."
set_summary product industrial-doors "High speed doors, roller shutters, sectional and steel hinged doors for industrial sites."
set_summary product gates-barriers "Gates and barriers that control who and what comes on to your site."
set_summary page service-maintenance "Breakdown response, safety inspections, force testing and remote monitoring for every door."
set_summary sector education "Safe, reliable access for schools, colleges and campuses with heavy daily footfall."
set_summary sector healthcare "Hygienic, hands-free entrances and fast repairs for hospitals, clinics and care settings."
set_summary sector retail-leisure "Welcoming, high-traffic entrances that stay open for business."
set_summary sector industrial-logistics "High speed and loading-bay doors built for round-the-clock operations."
set_summary sector commercial "Front-of-house automatic and revolving doors for offices and mixed-use buildings."
set_summary sector local-authority "Compliant, accessible entrances for council buildings, depots and public spaces."
set_summary sector high-sensitivity-sites "Secure, controlled access for data centres, utilities and critical infrastructure."
set_summary sector block-residential "Reliable communal entrances and gates for managed residential blocks."
