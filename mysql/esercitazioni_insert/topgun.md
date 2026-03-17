

# Film: Top Gun
# Release Year: 1986
# Description: As students at the United States Navy's elite fighter weapons school compete to be best in the class, one daring young pilot learns a few things from a civilian instructor that are not taught in the classroom.
# Category: Action, Drama
# Rental duration: 6
# Rental rate: 4.99
# length: 110
# replacement cost: 20.99
# rating: G
# Special features: Trailers, Commentaries
# Film Text: As students at the United States Navy's elite fighter weapons school compete to be best in the class, one daring young pilot learns a few things from a civilian instructor that are not taught in the classroom.



```sql

SELECT * FROM film;

INSERT INTO film (
  title,
  release_year,
  description,
  rental_duration,
  rental_rate,
  length,
  replacement_cost,
  rating,
  special_features,
  language_id,
  original_language_id
) VALUES (
  'Top Gun 2',
  '1986',
  'As students at the United States Navy\'s elite fighter weapons school compete to be best in the class, one daring young pilot learns a few things from a civilian instructor that are not taught in the classroom.',
  '6',
  '4.99',
  '110',
  '20.99',
  'G',
  'Trailers',
  1,
  1
);
INSERT INTO actor (first_name, last_name) VALUES ('Tom', 'Cruise');
INSERT INTO film_actor (film_id, actor_id) VALUES (1004, 1005);
INSERT INTO film_category (category_id, film_id) VALUES (1, 1004);
INSERT INTO film_category (category_id, film_id) VALUES (7, 1004);


```