=== TikTok Profile Embed ===
Contributors: ugcsuomi
Tags: tiktok, embed, social media, oembed, profile
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Upottaa TikTok-käyttäjän profiilin ja videot WordPress-sivuille shortcodella tai Gutenberg-lohkolla.

== Description ==

TikTok Profile Embed on kevyt WordPress-plugin, joka upottaa TikTok-sisällöntuottajan profiilin virallisen oEmbed-upotuksen avulla. Plugin toimii missä tahansa WordPress-ympäristössä eikä vaadi TikTok API -avainta.

**Ominaisuudet:**

* Shortcode: `[tiktok_profile username="kayttajanimi"]`
* Gutenberg-lohko: TikTok Profile Embed
* Transient-välimuisti suorituskyvyn parantamiseksi
* Stale cache -fallback jos TikTok ei vastaa
* Valinnainen lazy load
* Responsiivinen upotus

== Installation ==

1. Kopioi `tiktok-profile-embed`-kansio WordPressin `wp-content/plugins/`-hakemistoon.
2. Aktivoi plugin WordPressin **Plugins**-valikosta.
3. Lisää upotus sivulle joko shortcodella tai Gutenberg-lohkolla.

== Usage ==

**Shortcode**

`[tiktok_profile username="sara_rai"]`
`[tiktok_profile username="sara_rai" height="600"]`

**Gutenberg**

1. Avaa sivu editorissa.
2. Lisää lohko **TikTok Profile Embed**.
3. Anna TikTok-käyttäjänimi (ilman @-merkkiä).

**Ugcsuomi.fi-profiilisivu**

1. Asenna ja aktivoi plugin WordPressiin.
2. Avaa haluamasi sisällöntuottajan profiilisivu (esim. `/ugc-sisallontuottajat/sara-rai/`).
3. Lisää Gutenberg-lohko tai shortcode profiilin TikTok-käyttäjänimellä.
4. Julkaise muutokset.

== Settings ==

Asetukset löytyvät kohdasta **Settings → TikTok Profile Embed**:

* Cache-aika tunteina (oletus 24 h)
* Oletuskorkeus pikseleinä (oletus 600 px)
* Lazy load päälle/pois
* Cache-tyhjennys

== Frequently Asked Questions ==

= Tarvitseeko plugin TikTok API -avaimen? =

Ei. Plugin käyttää TikTokin virallista oEmbed-rajapintaa.

= Toimiiko yksityisillä TikTok-tileillä? =

Ei. Vain julkiset profiilit voidaan upottaa.

= Lataako upotus kolmannen osapuolen evästeitä? =

Kyllä. TikTok-upotus lataa TikTokin skriptejä ja voi asettaa evästeitä. Harkitse evästebannerin ja suostumuslogiikan käyttöä sivustollasi.

== Changelog ==

= 1.0.1 =
* Fix: TikTok embed.js now loads after Elementor/shortcode render (footer)
* Fix: Preserve data-embed-type attribute required for creator profile embeds
* Fix: Clear stale embed cache automatically on plugin update

= 1.0.0 =
* Ensimmäinen julkaisu
* Shortcode ja Gutenberg-lohko
* oEmbed-haku, cache ja lazy load

== Upgrade Notice ==

= 1.0.0 =
Ensimmäinen julkaisu.
