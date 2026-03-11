SELECT * FROM `transactie` 
INNER JOIN categorie ON transactie.categorie_id = categorie.id 
WHERE STR_TO_DATE(transactie.datum, '%Y-%m-%d') BETWEEN '2026-01-01' AND '2026-01-31'
AND categorie.exclude = false
AND richting = "In"

ORDER by transactie.datum DESC;