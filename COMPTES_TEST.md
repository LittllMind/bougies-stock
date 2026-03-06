# Comptes de Test - Projet Vinyls

## Utilisateurs créés pour tester le RBAC

### Admin
- **Email** : admin@example.com
- **Mot de passe** : password
- **Rôle** : admin
- **Accès** : Toutes les routes (/vinyles, /stats, /fonds, /ventes, /kiosque)

### Employé
- **Email** : employe@example.com
- **Mot de passe** : password
- **Rôle** : employe
- **Accès** : Routes employé + /kiosque

### Client
- **Email** : client@example.com
- **Mot de passe** : password
- **Rôle** : client
- **Accès** : /kiosque uniquement

## Tests à effectuer

### ✅ Validés
- [x] Middleware CheckRole créé
- [x] Utilisateurs de test créés
- [x] Rôles cohérents (employe en français)

### 🔄 À tester
- [ ] Connexion avec admin@example.com et vérification des routes admin
- [ ] Connexion avec employe@example.com et vérification des restrictions
- [ ] Connexion avec client@example.com et vérification de l'accès limité au kiosque
- [ ] Vérifier que /kiosque est accessible par tous les utilisateurs authentifiés
- [ ] Vérifier que l'achat nécessite une connexion

## Notes
- Date de création : 2026-03-05
- Tous les mots de passe sont "password" pour faciliter les tests
- Ne pas utiliser en production !
