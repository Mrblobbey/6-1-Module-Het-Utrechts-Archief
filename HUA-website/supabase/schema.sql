-- Supabase (Postgres) schema voor de HUA panorama-viewer.
-- Voer uit in Supabase Dashboard → SQL Editor → New query.

create table if not exists artikel (
  id              bigserial primary key,
  catalogusnummer text,
  beschrijving    text,
  link_bron       text,
  afbeelding      text,
  alt             text,
  x               integer,
  y               integer,
  polygons        jsonb,
  height          integer  default 489,
  margin_left     integer  default 0,
  margin_top      integer  default 0,
  z_index         integer  default 0,
  created_at      timestamptz default now()
);

create table if not exists artikelaanvullend (
  id           bigserial primary key,
  artikel_id   bigint references artikel(id) on delete cascade,
  beschrijving text,
  link_bron    text,
  x            integer,
  y            integer,
  afbeelding1  text,
  afbeelding2  text,
  afbeelding3  text,
  afbeelding4  text,
  created_at   timestamptz default now()
);

-- Iedereen mag lezen (publieke panorama). Alleen ingelogde gebruikers
-- mogen muteren (later, voor het CMS).
alter table artikel              enable row level security;
alter table artikelaanvullend    enable row level security;

create policy "public read artikel"
  on artikel for select
  using (true);

create policy "public read artikelaanvullend"
  on artikelaanvullend for select
  using (true);
