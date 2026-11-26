# Infoboard

Deux applications web 100 % front, chacune dans **un seul fichier HTML** (sans dépendance, sans serveur applicatif, sans appel à un service externe) :

| Fichier | Rôle |
|---|---|
| `infoboard-editeur.html` | Composer un grand board d'infographies : images, liens de téléchargement, textes, positions. |
| `infoboard-lecteur.html` | Afficher une composition en lecture seule : vue d'ensemble, zoom, infographie agrandie, liens cliquables. |

Seules les données de composition sont séparées des deux applications.

---

## Démarrage rapide

1. **Composer** : ouvrez `infoboard-editeur.html` dans un navigateur (un double-clic suffit). Glissez-déposez vos infographies sur le board, ajoutez des textes et des liens.
2. **Exporter** : bouton **Exporter**, puis **Pack pour le lecteur (.zip)**. Vous obtenez par exemple `cours-vuejs.zip`.
3. **Publier** : décompressez le `.zip` à côté du lecteur, puis ouvrez :

   ```
   infoboard-lecteur.html?c=cours-vuejs/
   ```

---

## L'éditeur

### Ajouter du contenu

| Élément | Comment |
|---|---|
| **Images** | Bouton image de la barre de gauche (`I`), glisser-déposer depuis l'explorateur de fichiers, ou coller (`Ctrl V`). Plusieurs images importées d'un coup sont rangées en grille. Formats : PNG, JPG, GIF, WebP, SVG, AVIF. |
| **Liens** | Bouton lien (`L`) : une adresse seule, ou une adresse et un titre. Glisser ou coller une URL crée aussi un lien. L'icône s'adapte : téléchargement pour un fichier (`.pdf`, `.zip`, `.docx`…), lien externe sinon. |
| **Textes** | Bouton texte (`T`) ou double-clic dans le vide. Plusieurs lignes possibles (`Entrée`). `Échap` ou un clic ailleurs termine la saisie. |

Adresses de lien acceptées : `https://…`, `http://…`, `mailto:…`, ou un **chemin relatif** (`fichiers/support.pdf`). Un chemin relatif part du dossier de la composition (voir [Liens relatifs](#liens-relatifs)).

### Organiser

- **Déplacer** : glisser un élément. Il s'aimante aux bords et aux centres des autres éléments (repères roses) ; maintenez `Alt` pour désactiver le magnétisme. Flèches du clavier pour ajuster finement (`Maj` : par 10).
- **Redimensionner** : tirer un des quatre coins de la sélection. Pour une image, les proportions sont conservées ; pour un texte ou un lien, c'est la **taille du texte** qui change. La taille d'un texte peut aussi se saisir dans la barre contextuelle.
- **Barre contextuelle** (au-dessus de la sélection) : couleur et gras du texte, modification d'un lien, premier plan / arrière-plan, dupliquer, supprimer.
- **Sélection multiple** : `Maj` + clic, ou `Maj` + glisser dans le vide pour tracer un rectangle. `Ctrl A` sélectionne tout.
- **Annuler / rétablir** : `Ctrl Z` / `Ctrl Maj Z`.
- **Couleur de fond** du board : menu `⋯`.

### Naviguer

- Molette ou pincement : zoom centré sur le curseur.
- Glisser dans le vide, ou `Espace` + glisser : se déplacer.
- `F` ou le bouton de recentrage : cadrer tout le contenu. `0` : zoom à 100 %.

### Sauvegarde automatique

Le board est enregistré en continu **dans le navigateur** (IndexedDB) et restauré à la réouverture de l'éditeur. Cette sauvegarde est propre à ce navigateur sur cet ordinateur : vider les données du site l'efface. **Exportez régulièrement.**

### Exporter

| Export | Contenu | Usage |
|---|---|---|
| **Pack pour le lecteur (.zip)** | `composition.json`, `composition.js`, `images/` (vos fichiers d'origine), `LISEZMOI.txt` | Publication avec le lecteur, versionnement Git. **Format recommandé.** |
| **Sauvegarde tout-en-un (`…-sauvegarde.json`)** | Un seul fichier, images intégrées en base64 (environ +33 % de poids) | Rouvrir le board plus tard, l'envoyer à quelqu'un, ou le publier en un fichier sur un serveur web. |

`composition.js` contient exactement les mêmes données que `composition.json` : il permet au lecteur de fonctionner quand on l'ouvre en double-clic, sans serveur web.

### Rouvrir une composition

- **Ouvrir** : un `.zip` exporté ou un `.json` tout-en-un.
- **Dossier publié** (déjà décompressé, par exemple dans un dépôt Git) : glissez le dossier sur le board, ou menu `⋯` › **Ouvrir un dossier publié**.

Les identifiants des éléments et les noms des images sont conservés : ré-exporter sans rien modifier redonne le même `composition.json`, hors date d'export (les dimensions mesurées des textes peuvent varier légèrement d'un ordinateur à l'autre, selon les polices installées).

### Modifier directement un dossier : `?c=`

Comme le lecteur, l'éditeur accepte le paramètre `c` pour ouvrir un dossier de composition du site :

```
infoboard-editeur.html?c=vuejs/
```

Le bouton **Enregistrer** (`Ctrl S`) remplace alors l'export : il réécrit `composition.json` et `composition.js` dans ce dossier, ajoute les nouvelles images dans `images/` et supprime celles qui ne sont plus utilisées. Le reste du dossier (par exemple `fichiers/`) n'est jamais touché. La sauvegarde automatique du navigateur est désactivée dans ce mode, et le navigateur prévient avant de quitter la page s'il reste des modifications non enregistrées.

L'écriture est assurée par [`save.php`](save.php), qui doit rester à côté de l'éditeur. Il faut donc servir le site avec PHP, en relevant les limites d'envoi pour les grosses images :

```
php -d upload_max_filesize=64M -d post_max_size=256M -S localhost:8000
```

Seul un **dossier déjà existant contenant `composition.json`** peut être enregistré ; `.zip` et `.json` ne sont pas acceptés dans ce mode. `save.php` est un outil de travail local : ne le publiez pas (ni sur GitHub Pages, qui n'exécute pas PHP, ni sur un serveur ouvert à d'autres personnes).

### Raccourcis clavier

Sur Mac, `Ctrl` correspond à `⌘` et `Alt` à `⌥`.

| Action | Raccourci |
|---|---|
| Importer des images / ajouter un lien / ajouter un texte | `I` / `L` / `T` |
| Modifier le texte ou le lien sélectionné | `Entrée` ou double-clic |
| Supprimer | `Suppr` |
| Dupliquer / copier / coller | `Ctrl D` / `Ctrl C` / `Ctrl V` |
| Tout sélectionner | `Ctrl A` |
| Annuler / rétablir | `Ctrl Z` / `Ctrl Maj Z` |
| Zoomer / dézoomer / 100 % / recentrer | `+` / `−` / `0` / `F` |
| Exporter / ouvrir | `Ctrl S` / `Ctrl O` |
| Aide | `?` |

---

## Le lecteur

### Choisir la composition : le paramètre `c` de l'adresse

Par défaut, la composition est indiquée dans l'adresse de la page, sous l'une de ces trois formes :

| Forme | Exemple | Ce que lit le lecteur |
|---|---|---|
| **Dossier** | `infoboard-lecteur.html?c=cours-vuejs/` | `cours-vuejs/composition.json` et `cours-vuejs/images/…` (la barre finale est facultative) |
| **Fichier .json** (nom libre) | `infoboard-lecteur.html?c=archives/vuejs-2026.json` | Ce fichier ; ses images sont des chemins relatifs à lui, ou intégrées (export tout-en-un) |
| **Archive .zip** (nom libre) | `infoboard-lecteur.html?c=cours-vuejs.zip` | Le `composition.json` de l'archive (à la racine ou dans un sous-dossier) et les images qu'elle contient |

Règles :

- Le chemin est relatif à la page du lecteur ; les sous-dossiers sont permis (`?c=2026/s1/vuejs.zip`).
- Pour des raisons de sécurité, l'adresse ne peut désigner qu'un fichier **du même site** (pas de `https://autre-site…`).
- Sans paramètre, le lecteur affiche un écran d'accueil où l'on peut saisir le chemin.
- Un seul lecteur suffit pour plusieurs cours : `?c=vuejs/`, `?c=nodejs/`, `?c=dom.zip`…

### Figer la composition : `COMPOSITION_PATH`

En haut du script de `infoboard-lecteur.html` :

```js
const COMPOSITION_PATH = '';          // vide par défaut : la composition vient de l'adresse
```

Si vous y indiquez une composition (mêmes trois formes, ou une adresse complète), elle **l'emporte sur l'adresse** :

```js
const COMPOSITION_PATH = 'cours-vuejs/';
```

Pratique pour une page dédiée à un seul cours (par exemple un `index.html` qui n'affiche que lui).

### Double-clic ou serveur web ?

Un navigateur qui ouvre une page en double-clic (`file://…`) interdit à cette page de lire d'autres fichiers par programme. Le lecteur contourne cette limite grâce à `composition.js`, mais seulement pour un dossier.

| Forme | Double-clic (`file://`) | Serveur web (GitHub Pages, hébergement…) |
|---|---|---|
| Dossier décompressé (avec `composition.js`) | ✅ | ✅ |
| Fichier `.json` | ❌ | ✅ |
| Archive `.zip` | ❌ | ✅ |

En cas d'échec, la page explique ce qu'elle cherchait et propose **Choisir un .zip ou un .json…** (ou le glisser-déposer) pour un aperçu immédiat sur l'ordinateur.

Pour tester les trois formes en local, lancez un petit serveur dans le dossier, par exemple `python -m http.server`, puis ouvrez `http://localhost:8000/infoboard-lecteur.html?c=…`.

### Côté étudiants

- **Clic (ou toucher) sur une infographie** : elle est agrandie à l'écran. Nouveau clic ou `Échap` : retour à la vue d'ensemble.
- **`←` `→`** (ou les flèches de la barre du bas) : infographie précédente / suivante, dans l'ordre de lecture (rangées de haut en bas, puis de gauche à droite).
- Molette, pincement et glisser pour explorer librement ; `F` pour revenir à la vue d'ensemble ; bouton plein écran.
- Les liens s'ouvrent dans un nouvel onglet ; un lien vers un fichier du même site se télécharge directement.
- Navigation au clavier possible : `Tab` passe d'une infographie ou d'un lien à l'autre, `Entrée` agrandit ou ouvre.

### Liens relatifs

Un lien relatif comme `fichiers/support.pdf` est cherché :

- **dossier ou .json** : à partir du dossier qui contient le `composition.json` (ou le `.json`) ;
- **.zip** : d'abord **dans l'archive** (le fichier est alors téléchargé depuis l'archive), sinon à côté du `.zip` sur le serveur.

Vous pouvez donc ajouter un dossier `fichiers/` dans le dossier publié, ou dans le `.zip`, pour y ranger les PDF et archives d'exercices.

---

## Publier sur GitHub Pages

### Organisation conseillée du dépôt

```
mon-depot/
├─ index.html            ← infoboard-lecteur.html renommé
├─ .nojekyll             ← fichier vide : Pages sert les fichiers tels quels
├─ vuejs/                ← contenu décompressé du pack
│  ├─ composition.json
│  ├─ composition.js
│  ├─ images/
│  └─ fichiers/          ← vos PDF, archives d'exercices… (ajoutés à la main)
└─ nodejs/
   └─ …
```

Adresses obtenues : `https://votre-compte.github.io/mon-depot/?c=vuejs/`, `…/?c=nodejs/`.

Activez Pages dans les réglages du dépôt (**Settings › Pages**), en publiant la branche principale depuis la racine.

### Dossier ou .zip dans le dépôt ?

Versionnez le **dossier décompressé** plutôt que le `.zip` :

- `composition.json` est un texte indenté, une propriété par ligne : `git diff` montre exactement ce qui a bougé (une position, un titre) ;
- les images ne changent pas d'un export à l'autre (mêmes noms, mêmes octets) : Git ne les stocke qu'une fois ;
- côté étudiants, le board s'affiche tout de suite et les images arrivent une à une, puis restent en cache après une mise à jour.

Le `.zip` publié tel quel (`?c=vuejs.zip`) fonctionne aussi, mais chaque modification réécrit toute l'archive et les étudiants doivent la télécharger entièrement avant de voir le board. Évitez le `.json` tout-en-un dans un dépôt : un seul gros fichier illisible, qui grossit vite (GitHub avertit au-delà de 50 Mo par fichier et refuse au-delà de 100 Mo).

### Mettre à jour une composition

1. Ouvrez l'éditeur et glissez-y le dossier `vuejs/`.
2. Modifiez, puis **Exporter › Pack pour le lecteur (.zip)**.
3. Dans `vuejs/`, supprimez `composition.json`, `composition.js` et `images/` (**gardez `fichiers/`**), puis décompressez le `.zip` à la place. Sans cette suppression, une image retirée du board resterait dans le dépôt.
4. Vérifiez avec `git diff`, puis `git commit` et `git push`. Ne committez pas le `.zip` d'export.

Attention : contrairement à Windows, GitHub Pages distingue majuscules et minuscules dans les adresses. L'éditeur nomme les images en minuscules ; vérifiez l'orthographe exacte des fichiers visés par vos liens relatifs.

---

## Format `composition.json` (version 1)

```json
{
  "format": "infoboard",
  "version": 1,
  "title": "Développement front avec Vue.js",
  "background": "#ffffff",
  "exportedAt": "2026-11-19T18:00:00.000Z",
  "bounds": { "x": -1700, "y": -900, "w": 3400, "h": 1800 },
  "elements": [
    { "id": "i1", "type": "image", "x": 0, "y": 0, "w": 800, "h": 1111,
      "src": "images/composants-vue.png", "alt": "composants vue" },
    { "id": "t1", "type": "text", "x": 0, "y": -150, "w": 1300, "h": 140,
      "text": "Développement front avec Vue.js", "fontSize": 120, "color": "#1c2230", "bold": true },
    { "id": "l1", "type": "link", "x": 0, "y": 1140, "w": 560, "h": 110,
      "url": "fichiers/composants-vue.pdf", "title": "Fiche à imprimer (PDF)", "fontSize": 49 }
  ]
}
```

| Champ | Description |
|---|---|
| `elements` | Ordre = superposition : le dernier élément est au premier plan. |
| `x`, `y`, `w`, `h` | Position et taille dans le repère du board (unités libres, le lecteur cadre automatiquement). Pour les textes et les liens, `w` et `h` sont indicatifs : le lecteur les mesure. |
| `src` (image) | Chemin relatif au fichier `composition.json`, adresse `https://…`, ou `data:image/…` (export tout-en-un). |
| `alt` (image) | Texte alternatif, déduit du nom de fichier. |
| `text`, `fontSize`, `color`, `bold` (texte) | Contenu (sauts de ligne `\n` permis), taille en unités du board, couleur `#rrggbb`, gras. |
| `url`, `title`, `fontSize` (lien) | Adresse (`http(s)`, `mailto` ou chemin relatif), titre facultatif (à défaut, le nom du fichier ou l'adresse), taille du texte. |

`composition.js` contient la même chose sous la forme `window.INFOBOARD_COMPOSITION = { … };`.

Le fichier peut être écrit ou retouché à la main : les champs inconnus sont ignorés, les valeurs invalides remplacées par des valeurs par défaut.

---

## Dépannage

| Symptôme | Cause probable | Solution |
|---|---|---|
| « La composition n'a pas pu être chargée », le dossier contient un `.zip` | Le pack n'a pas été décompressé | Clic droit sur le `.zip`, « Extraire tout » dans ce dossier, ou indiquez le `.zip` lui-même (sur un serveur). |
| Ça marche en ligne mais pas en double-clic avec `?c=….zip` ou `?c=….json` | Le navigateur interdit cette lecture en `file://` | Utilisez le dossier décompressé, un petit serveur local, ou le bouton d'aperçu. |
| Erreur 404 sur GitHub Pages | Chemin ou casse différents | Comparez le nom exact (majuscules comprises) avec le dépôt. |
| « l'adresse ne peut désigner qu'un dossier ou un fichier de ce site » | `?c=` pointe vers un autre site | Copiez la composition sur le même site que le lecteur. |
| Images manquantes après « Ouvrir » dans l'éditeur | Un `composition.json` ouvert seul, sans ses images | Ouvrez le `.zip`, le `.json` tout-en-un, ou glissez le **dossier** entier. |
| Le board a disparu de l'éditeur | Données du navigateur effacées, autre navigateur ou autre ordinateur | Rouvrez votre dernier export (`.zip`, `.json` ou dossier publié). |

---

## Sécurité et confidentialité

- Aucune dépendance ni ressource externe : les deux pages fonctionnent hors ligne.
- Lecteur : les textes sont affichés comme du texte, jamais interprétés ; seuls les liens `http(s)`, `mailto` et relatifs sont acceptés (`javascript:` est refusé) ; le paramètre `c` ne peut viser que le même site.
- Éditeur : les images restent sur votre ordinateur (mémoire du navigateur) jusqu'à ce que vous exportiez.

## Compatibilité

Navigateurs récents : Chrome, Edge, Firefox, Safari. Sur mobile et tablette, le lecteur gère le toucher et le pincement ; l'éditeur est pensé pour la souris et le clavier. La lecture des `.zip` compressés par un autre outil (Windows, macOS) nécessite un navigateur de 2023 ou plus récent.
