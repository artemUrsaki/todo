Docs:

Autentifikácia

Používa sa Laravel Sanctum, takže po registrácii potrebujete máť API-token v Header pri requeste.


POST /register
Registruje nového používateľa a vráti údaje o používateľovi spolu s autentifikačným tokenom.

Vstup:
{
  "name": "Example Surname",
  "email": "example@example.com",
  "password": "password"
}



POST /login
Autentifikuje existujúceho používateľa a vráti autentifikačný token.

Vstup:
{
  "email": "john@example.com",
  "password": "securePassword123"
}

Vystup:
200 OK - Vráti údaje o používateľovi a token.
401 Unauthorized - Nesprávne prihlasovacie údaje.



GET /users
Vráti zoznam všetkých registrovaných používateľov. Autentifikácia nie je potrebná.



GET /tasks
Vráti stránkovaný zoznam všetkých úloh pre autentifikovaného používateľa. Podporuje filtrovanie podľa stavu dokončenia a kategórie.

Pre filtrovanie sa používa spatie/laravel-query-builder package. Aby filtrovať výstup je potrebné použiť "filter[hodnota]" ako query parameter
Príklad:

localhost:8000/api/tasks?filter[is_completed]=1&filter[category]=menoCategorii



GET /tasks/own
Vráti úlohy, ktoré vytvoril autentifikovaný používateľ. Je možné filtrovanie.



GET /tasks/shared
Vráti úlohy, ktoré sú zdieľané s autentifikovaným používateľom. Je možné filtrovanie.



GET /tasks/{task_id}
Vráti detaily konkrétnej úlohy.

Výstup:
200 OK - Vráti detaily úlohy.
403 Forbidden - Používateľ nemá oprávnenie zobraziť úlohu.



POST /tasks
Vytvorí novú úlohu.

Vstup:
{
  "name": "New Task",
  "content": "Task details"
}

Výstup:
201 Created - Vráti vytvorenú úlohu.
400 Bad Request - Chyby validácie.



PUT/PATCH /tasks/{task_id}
Aktualizuje existujúcu úlohu.

Vstup PUT:
{
  "name": "Updated name",
  "content": "Updated content"
}

V prípade PATCH je potrebné ukázať aspoň jednu vstupnú hodnotu:
{
  "name": "Updated name"
}

Výstup:
200 OK - Úloha bola úspešne aktualizovaná.
403 Forbidden - Používateľ nemá oprávnenie aktualizovať úlohu.



DELETE /tasks/{task_id}
Vymaže úlohu.

Výstup:
200 OK - Úloha bola úspešne vymazaná.
403 Forbidden - Používateľ nemá oprávnenie vymazať úlohu.



POST /tasks/{id}/restore
Obnoví predtým vymazanú úlohu.

Výstup:
200 OK - Úloha bola obnovená.
403 Forbidden - Používateľ nemá oprávnenie obnoviť úlohu.



POST /tasks/{task_id}/share
Zdieľa úlohu s iným používateľom.

Vstup:
{
  "user_id": 2 //používateľ, s ktorým chceme zdieľať úlohu
}

Výstup:
200 OK - Úloha bola úspešne zdieľaná.
403 Forbidden - Používateľ nemá oprávnenie zdieľať úlohu.



POST /tasks/{task_id}/unshare
Zruší zdieľanie úlohy s konkrétnym používateľom.

Vstup:
{
  "user_id": 2 //používateľ, s ktorým chceme zrušiť zdieľanie
}

Výstup:
200 OK - Úloha bola úspešne zdieľaná.
403 Forbidden - Používateľ nemá oprávnenie nezdieľať úlohu.



POST /tasks/{task_id}/setCategory
Priradí kategóriu k úlohe.

Vstup:
{
  "category": "Sport"
}

Výstup:
200 OK - Kategória bola priradená k úlohe.
403 Forbidden - Používateľ nemá oprávnenie priradiť kategóriu k úlohe.



POST /tasks/{task}/unsetCategory
Odstráni kategóriu z úlohy.

Vstup:
{
  "category": "Sport"
}

Výstup:
200 OK - Kategória bola priradená k úlohe.
403 Forbidden - Používateľ nemá oprávnenie odstrániť kategóriu z úlohy.

Pri každom requeste sa okrem zobrazovania úloh alebo userov (/tasks, /tasks/{id}, /tasks/own, /tasks/shared, /users) a autentifikácii (/login, /register) odošle e-mail používateľovi.