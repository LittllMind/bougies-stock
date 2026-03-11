## 2026-03-11 16:58 — T13.3 Sécurité Fonds (IDOR)
**Statut** : 🔄 En cours

**Résumé** :
Implémentation des protections IDOR sur la gestion des fonds. Les clients ne doivent pas pouvoir consulter ou modifier les fonds. Les employés peuvent consulter mais pas modifier.

**Fichiers créés/modifiés** :
- ✅ `tests/Feature/Security/FondIdorTest.php` — Tests IDOR complets (7 tests)
- ✅ `routes/web.php` — Ajout middleware admin sur route `/fonds/{fond}/prix`
- ✅ `app/Http/Controllers/FondController.php` — Correction `updatePrix()` avec `abort(403)`

**Tests créés** :
1. `test_client_cannot_access_fonds_list` — Client = 403
2. `test_employe_can_view_fonds_list` — Employé = OK
3. `test_employe_cannot_modify_fond_stock` — Employé modifie = 403
4. `test_employe_cannot_update_fond_prices` — Employé prix = 403
5. `test_admin_can_modify_any_fond` — Admin modifie = OK
6. `test_admin_can_update_fond_prices` — Admin prix = OK
7. `test_client_cannot_modify_fond_stock` — Client modifie = 403

**Pour tester** :
```bash
# Lancer uniquement les tests IDOR
php artisan test tests/Feature/Security/FondIdorTest.php

# Lancer tous les tests de sécurité
php artisan test tests/Feature/Security/
```

**Notes** :
- La route `/fonds` était déjà protégée par middleware `role:admin,employe`
- La route `/fonds/{fond}/prix` n'était pas protégée, ajout du middleware dans le constructeur de route
- Le contrôleur `updatePrix()` retournait un redirect au lieu d'un abort(403), corrigé pour les tests

**Prochaine étape** : Exécuter les tests et s'assurer qu'ils passent tous