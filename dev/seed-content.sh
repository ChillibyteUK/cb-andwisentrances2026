#!/bin/bash
set -e
W() { docker exec -w /www/kinsta/public/andwisentrancescom devkinsta_fpm wp --allow-root "$@"; }
W option update blogname "andwis entrances" >/dev/null
W option update blogdescription "Automatic, industrial and revolving doors - installed, serviced and maintained." >/dev/null
W rewrite structure '/news/%postname%/' >/dev/null 2>&1 || true
W eval 'update_option("cb_andwisentrances2026_site_settings", array(
 "tagline" => "Automatic, industrial and revolving doors, gates and barriers - installed, serviced and maintained across the UK.",
 "group_line" => "An andwis Group company",
 "group_url" => "https://www.andwis.com",
 "email" => "hello@andwisentrances.com",
 "phone" => "0800 000 0000",
 "address" => "andwis entrances\nUnit 1, Example Business Park\nAscot SL5 0AA",
 "cta_heading" => "Ready to talk about your entrance?",
 "cta_text" => "Tell us about your building and we will recommend the right solution, with an honest view on cost and lead time.",
 "linkedin_url" => "https://www.linkedin.com/company/andwis",
 "registered_name" => "andwis entrances Limited",
 "place_of_registration" => "England and Wales",
 "custom_scripts_logged_out_only" => "1",
));'
mk() { W post create --post_type=page --post_status=publish --post_title="$1" --post_name="$2" ${3:+--post_parent=$3} --porcelain; }
HOME_ID=$(mk "Home" home); SM=$(mk "Service & Maintenance" service-maintenance); SEC=$(mk "Sectors" sectors); CS=$(mk "Case studies" case-studies)
NEWS=$(mk "News & resources" news); DL=$(mk "Downloads" downloads); ABOUT=$(mk "About" about); CAREERS=$(mk "Careers" careers)
CONTACT=$(mk "Contact" contact); QUOTE=$(mk "Get a Quote" get-a-quote); SURVEY=$(mk "Book a Survey" book-a-survey); LOC=$(mk "Locations" locations)
mk "Our story" our-story $ABOUT >/dev/null; mk "Etron is now andwis entrances" etron-is-now-andwis-entrances $ABOUT >/dev/null; mk "Accreditations" accreditations $ABOUT >/dev/null; mk "andwis Group" andwis-group $ABOUT >/dev/null; mk "Meet the team" meet-the-team $ABOUT >/dev/null
mk "Privacy policy" privacy-policy >/dev/null; mk "Cookie policy" cookie-policy >/dev/null; mk "Terms and conditions" terms-and-conditions >/dev/null; mk "Accessibility statement" accessibility-statement >/dev/null
W option update show_on_front page >/dev/null; W option update page_on_front $HOME_ID >/dev/null; W option update page_for_posts $NEWS >/dev/null
mp() { W post create --post_type=product --post_status=publish --post_title="$1" --post_name="$2" ${3:+--post_parent=$3} --menu_order=${4:-0} --porcelain; }
ARD=$(mp "Automatic & Revolving Doors" automatic-revolving-doors "" 1); IND=$(mp "Industrial Doors" industrial-doors "" 2); GB=$(mp "Gates & Barriers" gates-barriers "" 3)
for t in "Bi-folding doors:bi-folding-doors" "Sliding doors:sliding-doors" "Swing doors:swing-doors" "Automatic revolving doors:automatic-revolving-doors" "Manual revolving doors:manual-revolving-doors"; do mp "${t%%:*}" "${t##*:}" $ARD >/dev/null; done
for t in "High speed doors:high-speed-doors" "Roller shutters:roller-shutters" "Sectional overhead doors:sectional-overhead-doors" "Steel hinged doors:steel-hinged-doors"; do mp "${t%%:*}" "${t##*:}" $IND >/dev/null; done
ms() { W post create --post_type=service --post_status=publish --post_title="$1" --post_name="$2" --menu_order=$3 --porcelain >/dev/null; }
ms "Breakdown & repair" breakdown-repair 1; ms "Safety inspections & force testing" safety-inspections-force-testing 2; ms "Remote monitoring & IoT" remote-monitoring-iot 3
i=0; for t in "Education:education" "Healthcare:healthcare" "Retail & Leisure:retail-leisure" "Industrial & Logistics:industrial-logistics" "Commercial:commercial" "Local Authority:local-authority" "High Sensitivity Sites:high-sensitivity-sites" "Block Residential:block-residential"; do i=$((i+1)); W post create --post_type=sector --post_status=publish --post_title="${t%%:*}" --post_name="${t##*:}" --menu_order=$i --porcelain >/dev/null; done
# Menus
PM=$(W menu create "Primary" --porcelain); FM=$(W menu create "Footer" --porcelain); FC=$(W menu create "Footer Contact" --porcelain)
PROD=$(W menu item add-custom $PM "Products" "#" --porcelain)
W menu item add-post $PM $ARD --parent-id=$PROD >/dev/null; W menu item add-post $PM $IND --parent-id=$PROD >/dev/null; W menu item add-post $PM $GB --parent-id=$PROD >/dev/null
W menu item add-post $PM $SM >/dev/null
SECM=$(W menu item add-post $PM $SEC --porcelain)
for s in $(W post list --post_type=sector --fields=ID --format=csv --orderby=menu_order --order=ASC | tail -n +2); do W menu item add-post $PM $s --parent-id=$SECM >/dev/null; done
W menu item add-post $PM $CS >/dev/null
ABM=$(W menu item add-post $PM $ABOUT --porcelain)
for s in $(W post list --post_type=page --post_parent=$ABOUT --fields=ID --format=csv --orderby=title --order=ASC | tail -n +2); do W menu item add-post $PM $s --parent-id=$ABM >/dev/null; done
W menu item add-post $PM $NEWS >/dev/null
for p in $ARD $IND $GB $SM $SEC $CS $ABOUT $NEWS; do W menu item add-post $FM $p >/dev/null; done
for p in $CONTACT $LOC $CAREERS $QUOTE $SURVEY; do W menu item add-post $FC $p >/dev/null; done
W menu location assign $PM primary >/dev/null; W menu location assign $FM footer >/dev/null; W menu location assign $FC footer_contact >/dev/null
W rewrite flush >/dev/null
echo "HOME=$HOME_ID SM=$SM SEC=$SEC CS=$CS ABOUT=$ABOUT NEWS=$NEWS CAREERS=$CAREERS ARD=$ARD IND=$IND GB=$GB"
W post list --post_type=product --fields=ID,post_title,post_parent,url --format=table
