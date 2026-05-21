=== Instagram Profile Embed ===
Contributors: ugcsuomi
Tags: instagram, embed, social media, profile
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Upottaa Instagram-käyttäjän profiilin WordPress-sivuille shortcodella tai Gutenberg-lohkolla.

== Description ==

Instagram Profile Embed upottaa julkisen Instagram-profiilin virallisen /embed-iframe-upotuksen avulla. Plugin toimii missä tahansa WordPress-ympäristössä eikä vaadi Meta API -avainta.

**Ominaisuudet:**

* Shortcode: `[instagram_profile username="kayttajanimi"]`
* Gutenberg-lohko: Instagram Profile Embed
* Elementor Shortcode -widget yhteensopiva
* Valinnainen lazy load
* Responsiivinen iframe-upotus

== Installation ==

1. Kopioi plugin-kansio WordPressin `wp-content/plugins/`-hakemistoon nimellä `instagram-profile-embed`.
2. Aktivoi plugin WordPressin **Plugins**-valikosta.
3. Lisää upotus shortcodella tai Gutenberg-lohkolla.

== Usage ==

**Shortcode**

`[instagram_profile username="instagram"]`
`[instagram_profile username="instagram" height="600"]`

**Elementor**

1. Lisää **Shortcode**-widget.
2. Kirjoita: `[instagram_profile username="kayttajanimi"]`

== Frequently Asked Questions ==

= Tarvitseeko plugin Meta API -avaimen? =

Ei. Profiili-upotus käyttää Instagramin virallista iframe-upotusta (`instagram.com/username/embed`).

= Toimiiko yksityisillä tileillä? =

Ei. Vain julkiset profiilit, joissa upotus on sallittu, näkyvät.

= Miksi Instagram eroaa TikTok-pluginista? =

Instagram ei tarjoa samanlaista ilmaista profiili-oEmbed-API:a kuin TikTok. Tämä plugin käyttää Instagramin omaa iframe-upotusta, joka on virallinen tapa näyttää profiilia.

== Changelog ==

= 1.0.0 =
* Ensimmäinen julkaisu
