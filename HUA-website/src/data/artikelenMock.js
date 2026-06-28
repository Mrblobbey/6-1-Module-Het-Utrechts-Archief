// Mock-data die de DB-tabel `artikel` nabootst.
// Wordt later vervangen door een Supabase-call (zie composables/useArtikelen.js).

const voorbeeldPolygons = {
  3: [
    {
      name: 'Voorbeeldgebouw',
      catalogusnummer: '',
      title: '',
      beschrijving: '',
      link_bron: '',
      points: [
        { x: 280, y: 150 },
        { x: 440, y: 150 },
        { x: 440, y: 320 },
        { x: 280, y: 320 },
      ],
    },
  ],
}

export const artikelenMock = Array.from({ length: 33 }, (_, i) => {
  const id = i + 1
  const heeftHotspot = [3, 4, 10, 11, 12, 13, 14, 23, 24, 26, 31, 32].includes(id)
  return {
    id,
    catalogusnummer: `13500${1 + id}`,
    afbeelding: `${id}.jpg`,
    alt: `Panorama van Utrecht — deel ${id}`,
    beschrijving:
      heeftHotspot
        ? `Hotspot ${id} — historische locatie op het panorama van Utrecht (1859). Klik voor meer informatie.`
        : '',
    link_bron: heeftHotspot
      ? `https://hetutrechtsarchief.nl/onderzoek/resultaten?id=${id}`
      : '',
    x: heeftHotspot ? 120 : null,
    y: heeftHotspot ? 180 : null,
    polygons: voorbeeldPolygons[id] ?? null,
    height: 489,
    margin_left: 0,
    margin_top: 100,
    z_index: id,
  }
})
