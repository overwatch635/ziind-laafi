# API RESTful  Ziind-Laafi
## Architecte des routes  Faridha

## 1. Format JSON commun

### Reponse succes
```json
{
  "success": true,
  "data": {},
  "message": "Operation reussie"
}
```

### Reponse erreur
```json
{
  "success": false,
  "message": "Description de l'erreur",
  "errors": {
    "champ": ["Le champ est requis."]
  }
}
```

## 2. Codes HTTP

| Code | Cas d'usage |
|---|---|
| 200 OK | Lecture ou modification reussie |
| 201 Created | Cration reussie (POST) |
| 204 No Content | Suppression reussie |
| 400 Bad Request | Donnees invalides |
| 401 Unauthorized | Non authentifie |
| 403 Forbidden | Authentifie mais rele insuffisant |
| 404 Not Found | Ressource inexistante |
| 422 Unprocessable Entity | Erreurs de validation Laravel |
| 500 Internal Server Error | Erreur serveur |

## 3. Liste des routes API

### Auth (public / auth)
| Methode | Route | Description | Acces |
|---|---|---|---|
| POST | /api/register | Inscription client | Public |
| POST | /api/login | Connexion, retourne un token | Public |
| POST | /api/logout | Deconnexion | Auth |
| GET | /api/me | Profil de l'utilisateur connecte | Auth |

### Properties (biens)
| Methode | Route | Description | Acces |
|---|---|---|---|
| GET | /api/properties | Liste des biens (+ filtres) | Public |
| GET | /api/properties/{id} | Detail d'un bien | Public |
| POST | /api/properties | Creer une annonce | Agent, Bailleur |
| PUT | /api/properties/{id} | Modifier une annonce | Agent, Bailleur (proprietaire) |
| DELETE | /api/properties/{id} | Retirer une annonce | Agent, Bailleur, Manager |
| PATCH | /api/properties/{id}/validate | Valider une annonce bailleur | Agent |
| PATCH | /api/properties/{id}/reject | Refuser une annonce | Agent |

### Visits (demandes de visite)
| Methode | Route | Description | Acces |
|---|---|---|---|
| GET | /api/visits | Mes demandes de visite | Auth |
| POST | /api/properties/{id}/visits | Creer une demande de visite | Auth |
| PATCH | /api/visits/{id}/validate | Valider une visite | Agent |
| PATCH | /api/visits/{id}/reject | Refuser une visite | Agent |

### Favorites (favoris)
| Methode | Route | Description | Acces |
|---|---|---|---|
| GET | /api/favorites | Mes favoris | Auth |
| POST | /api/favorites/{propertyId} | Ajouter/retirer un favori | Auth |

### Users (gestion manager)
| Methode | Route | Description | Acces |
|---|---|---|---|
| GET | /api/manager/users | Liste des utilisateurs | Manager |
| POST | /api/manager/users | Creer un utilisateur (agent/manager) | Manager |
| PUT | /api/manager/users/{id} | Modifier un utilisateur | Manager |
| DELETE | /api/manager/users/{id} | Supprimer un utilisateur | Manager |

## 4. Diagramme des ressources

```
User (id, name, email, role: manager|agent|bailleur|client)
  |
  |-- 1..N --> Property (id, owner_id, title, type, status, ...)
  |               |
  |               |-- 1..N --> VisitRequest (id, property_id, user_id, status)
  |               |-- 1..N --> Favorite (id, property_id, user_id)
  |
  |-- 1..N --> VisitRequest (via user_id)
  |-- 1..N --> Favorite (via user_id)
```

**Relations :**
- Un `User` (bailleur/agent) possede plusieurs `Property`
- Une `Property` peut avoir plusieurs `VisitRequest` et `Favorite`
- Un `User` (client) peut avoir plusieurs `VisitRequest` et `Favorite`