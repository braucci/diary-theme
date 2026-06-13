# Diary

Tema WordPress autonomo per blog letterari, pensato per la lettura lunga.

![Versione](https://img.shields.io/badge/versione-1.0.0-2f6aa3)
![Licenza](https://img.shields.io/badge/licenza-GPL--2.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)

## Descrizione

**Diary** è un tema WordPress completo e indipendente (non è un child theme). Nasce dall'esperienza di [raucci.net](https://www.raucci.net/) ed è ottimizzato per la prosa: testo giustificato, tipografia *EB Garamond*, layout a colonna singola senza distrazioni.

La caratteristica distintiva è la **coerenza tipografica totale** tra la home e l'articolo singolo: gli stessi caratteri, la stessa dimensione e lo stesso allineamento giustificato vengono applicati in entrambe le viste, eliminando il fastidioso "salto" visivo tipico di molti temi quando si passa dall'elenco al post.

## Caratteristiche

- Layout minimal a colonna singola, centrato e leggibile
- Tipografia **EB Garamond** (corpo), **Space Grotesk** (interfaccia), **JetBrains Mono** (codice)
- Header blu con gradiente e barra di navigazione scura
- Testo **giustificato** con sillabazione automatica, coerente tra home e singolo
- Estratti automatici o manuali (campo "Riassunto" di WordPress)
- Meta tag **Open Graph** e **Twitter Cards** integrati
- Menu responsive con toggle su mobile
- Aree widget nel footer
- Supporto a immagini in evidenza, commenti threaded, post formats
- Stile editor Gutenberg coerente col front-end
- Pulsante "torna su"
- Pronto per la traduzione (text domain `diary`)
- Nessuna dipendenza esterna oltre i Google Fonts

## Requisiti

- WordPress 6.0 o superiore
- PHP 7.4 o superiore

## Installazione

### Da pacchetto ZIP

1. Scarica `diary.zip` dalla sezione [Releases](https://github.com/braucci/diary-theme/releases)
2. In WordPress: **Aspetto → Temi → Aggiungi nuovo → Carica tema**
3. Seleziona il file ZIP e clicca **Installa**
4. **Attiva** il tema

### Da Git

```bash
cd wp-content/themes/
git clone https://github.com/braucci/diary-theme.git diary
```

## Configurazione consigliata

1. **Aspetto → Menu** — crea un menu e assegnalo a "Menu principale"
2. **Impostazioni → Lettura** — imposta "Le pagine del blog visualizzano al massimo" a piacere
3. Per ogni articolo, compila il campo **Riassunto** se vuoi un estratto manuale in home; altrimenti il tema ne genera uno automatico (55 parole)

## Personalizzazione

### Citazione nel footer

Il footer mostra una citazione personalizzabile via filtro nel tuo `functions.php` o in un plugin:

```php
add_filter('diary_footer_quote', function () {
    return '<div class="squallor-quote"><p>La tua citazione</p></div>';
});
```

### Larghezza del contenuto

```php
add_filter('diary_content_width', function () {
    return 760; // px
});
```

### Colori e font

Le variabili sono definite in cima a `style.css` sotto `:root`. Modifica `--diary-blue-top`, `--diary-blue-bottom`, `--diary-font-body`, ecc.

## Struttura dei file

```
diary/
├── style.css              # Stili + intestazione tema
├── functions.php          # Setup, enqueue, Open Graph, widget
├── header.php             # Header con gradiente blu + nav
├── footer.php             # Footer con widget e citazione
├── index.php              # Home blog / fallback
├── single.php             # Articolo singolo
├── archive.php            # Archivi
├── page.php               # Pagina statica
├── search.php             # Risultati ricerca
├── 404.php                # Pagina non trovata
├── searchform.php         # Form di ricerca
├── comments.php           # Template commenti
├── template-parts/
│   ├── content.php        # Anteprima post (home/archivio)
│   ├── content-single.php # Articolo completo
│   └── content-none.php   # Nessun risultato
└── assets/
    ├── css/editor-style.css
    └── js/diary.js
```

## Licenza

Distribuito sotto licenza **GNU General Public License v2 o successiva**. Vedi il file [LICENSE](LICENSE).

I font sono distribuiti tramite Google Fonts sotto la SIL Open Font License.

## Autore

**Biagio Raucci** — [raucci.net](https://www.raucci.net/) · [github.com/braucci](https://github.com/braucci)

---

*"Si stava meglio quando c'erano gli Squallor."*
