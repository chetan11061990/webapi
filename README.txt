---------------DATABASE------------------------
Postgres

DATABASE : company
Schema : public

CREATE TABLE public.department (
	id serial4 not null unique,
	name varchar(80) default null
)
