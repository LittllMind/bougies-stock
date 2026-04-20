# API REST Publique - Bougies-Stock

## Endpoints Disponibles

### GET /api/bougies
Liste paginée des bougies en stock avec filtres.

**Paramètres de requête:**
| Paramètre | Type | Description | Défaut |
|-----------|------|-------------|--------|
| `collection` | string | Filtre par collection (Spirit, Art, Nature) | - |
| `prix_min` | float | Prix minimum | - |
| `prix_max` | float | Prix maximum | - |
| `search` | string | Recherche dans nom et parfum | - |
| `sort` | string | Champ de tri (nom, prix, quantite, reference) | `nom` |
| `order` | string | Ordre du tri (asc, desc) | `asc` |
| `per_page` | int | Items par page (max: 100) | `15` |
| `page` | int | Numéro de page | `1` |

**Réponse:**
```json
{
  "data": [
    {
      "id": 1,
      "reference": "BOUG-123",
      "nom": "Ganesh",
      "parfum": "Ambre",
      "collection": "Spirit",
      "format": "200g",
      "type_cire": "cire d'abeille 100% naturelle",
      "prix": 45.00,
      "quantite": 15,
      "stock_status": "en_stock"
    }
  ],
  "meta": {
    "total": 150,
    "per_page": 15,
    "current_page": 1,
    "last_page": 10
  }
}
```

### GET /api/bougies/{id}
### GET /api/bougies/{reference}
Détail d'une bougie par ID ou référence.

**Réponse:**
```json
{
  "data": {
    "id": 1,
    "reference": "BOUG-123",
    "nom": "Ganesh",
    "parfum": "Ambre",
    "collection": "Spirit",
    "format": "200g",
    "type_cire": "cire d'abeille 100% naturelle",
    "temps_brulure": 40,
    "notes": "Notes olfactives...",
    "prix": 45.00,
    "quantite": 15,
    "stock_status": "en_stock"
  }
}
```

### GET /api/categories
Liste des collections disponibles avec le nombre de bougies.

**Réponse:**
```json
{
  "data": [
    {
      "name": "Spirit",
      "count": 45
    },
    {
      "name": "Art",
      "count": 32
    },
    {
      "name": "Nature",
      "count": 28
    }
  ]
}
```

## Rate Limiting

- **Limite:** 60 requêtes par minute
- **Par:** IP ou utilisateur authentifié
- **Réponse 429:**
```json
{
  "message": "Trop de requêtes. Veuillez réessayer plus tard."
}
```

## Headers de réponse

| Header | Description |
|--------|-------------|
| `X-RateLimit-Limit` | Limite max (60) |
| `X-RateLimit-Remaining` | Requêtes restantes |
| `Retry-After` | Secondes avant nouvelle tentative (sur 429) |

## Notes

- Seules les bougies en stock (`quantite > 0`) sont retournées
- Les réponses sont toujours en JSON
- Les références suivent le format `BOUG-{timestamp}-{random}`
- Collections disponibles: Spirit, Art, Nature
