// Magic wand: klik op een pixel, flood-fill alle pixels met vergelijkbare kleur,
// vind de buitencontour van die regio en simplify naar een polygon van max ~30 punten.

function getPixel(data, w, x, y) {
  const i = (y * w + x) * 4
  return [data[i], data[i + 1], data[i + 2]]
}

function colorDist(a, b) {
  return Math.sqrt(
    (a[0] - b[0]) ** 2 + (a[1] - b[1]) ** 2 + (a[2] - b[2]) ** 2,
  )
}

function floodFill(data, w, h, startX, startY, tolerance, bbox = null) {
  const startColor = getPixel(data, w, startX, startY)
  const mask = new Uint8Array(w * h)
  const queue = [[startX, startY]]
  const minX = bbox ? bbox.minX : 0
  const minY = bbox ? bbox.minY : 0
  const maxX = bbox ? bbox.maxX : w - 1
  const maxY = bbox ? bbox.maxY : h - 1
  while (queue.length > 0) {
    const [x, y] = queue.pop()
    if (x < minX || y < minY || x > maxX || y > maxY) continue
    const idx = y * w + x
    if (mask[idx]) continue
    const c = getPixel(data, w, x, y)
    if (colorDist(c, startColor) > tolerance) continue
    mask[idx] = 1
    queue.push([x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1])
  }
  return mask
}

function traceContour(mask, w, h) {
  let startX = -1
  let startY = -1
  outer: for (let y = 0; y < h; y++) {
    for (let x = 0; x < w; x++) {
      if (mask[y * w + x]) {
        startX = x
        startY = y
        break outer
      }
    }
  }
  if (startX === -1) return []

  const dirs = [
    [1, 0],
    [1, 1],
    [0, 1],
    [-1, 1],
    [-1, 0],
    [-1, -1],
    [0, -1],
    [1, -1],
  ]
  const contour = []
  let x = startX
  let y = startY
  let dir = 0
  const maxSteps = w * h
  let steps = 0
  while (steps++ < maxSteps) {
    contour.push([x, y])
    let found = false
    for (let i = 0; i < 8; i++) {
      const d = (dir + 6 + i) % 8
      const [dx, dy] = dirs[d]
      const nx = x + dx
      const ny = y + dy
      if (nx < 0 || ny < 0 || nx >= w || ny >= h) continue
      if (mask[ny * w + nx]) {
        x = nx
        y = ny
        dir = d
        found = true
        break
      }
    }
    if (!found) break
    if (x === startX && y === startY && contour.length > 2) break
  }
  return contour
}

function perpendicularDistance(point, lineStart, lineEnd) {
  const [px, py] = point
  const [x1, y1] = lineStart
  const [x2, y2] = lineEnd
  const dx = x2 - x1
  const dy = y2 - y1
  if (dx === 0 && dy === 0) {
    return Math.sqrt((px - x1) ** 2 + (py - y1) ** 2)
  }
  const t = ((px - x1) * dx + (py - y1) * dy) / (dx * dx + dy * dy)
  const projX = x1 + t * dx
  const projY = y1 + t * dy
  return Math.sqrt((px - projX) ** 2 + (py - projY) ** 2)
}

function douglasPeucker(points, epsilon) {
  if (points.length < 3) return points
  let maxDist = 0
  let maxIdx = 0
  for (let i = 1; i < points.length - 1; i++) {
    const d = perpendicularDistance(points[i], points[0], points[points.length - 1])
    if (d > maxDist) {
      maxDist = d
      maxIdx = i
    }
  }
  if (maxDist > epsilon) {
    const left = douglasPeucker(points.slice(0, maxIdx + 1), epsilon)
    const right = douglasPeucker(points.slice(maxIdx), epsilon)
    return [...left.slice(0, -1), ...right]
  }
  return [points[0], points[points.length - 1]]
}

export function useMagicWand() {
  const cache = { src: null, canvas: null, ctx: null, data: null }

  async function prepare(imageSrc) {
    if (cache.src === imageSrc && cache.data) return cache
    const img = new Image()
    img.crossOrigin = 'anonymous'
    await new Promise((resolve, reject) => {
      img.onload = resolve
      img.onerror = reject
      img.src = imageSrc
    })
    const canvas = document.createElement('canvas')
    canvas.width = img.naturalWidth
    canvas.height = img.naturalHeight
    const ctx = canvas.getContext('2d', { willReadFrequently: true })
    ctx.drawImage(img, 0, 0)
    const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data
    cache.src = imageSrc
    cache.canvas = canvas
    cache.ctx = ctx
    cache.data = data
    cache.width = canvas.width
    cache.height = canvas.height
    return cache
  }

  async function detecteer(imageSrc, x, y, tolerance = 28, simplifyEpsilon = 4) {
    const c = await prepare(imageSrc)
    const px = Math.round(x)
    const py = Math.round(y)
    if (px < 0 || py < 0 || px >= c.width || py >= c.height) return null
    const mask = floodFill(c.data, c.width, c.height, px, py, tolerance)
    const contour = traceContour(mask, c.width, c.height)
    if (contour.length < 3) return null
    const simplified = douglasPeucker(contour, simplifyEpsilon)
    if (simplified.length < 3) return null
    return simplified.map(([x, y]) => ({ x, y }))
  }

  async function detecteerInBox(imageSrc, x1, y1, x2, y2, tolerance = 35, simplifyEpsilon = 12) {
    const c = await prepare(imageSrc)
    const minX = Math.max(0, Math.min(x1, x2))
    const maxX = Math.min(c.width - 1, Math.max(x1, x2))
    const minY = Math.max(0, Math.min(y1, y2))
    const maxY = Math.min(c.height - 1, Math.max(y1, y2))
    if (maxX - minX < 4 || maxY - minY < 4) return null

    const centerX = Math.round((minX + maxX) / 2)
    const centerY = Math.round((minY + maxY) / 2)
    const bbox = { minX, minY, maxX, maxY }
    let mask = floodFill(c.data, c.width, c.height, centerX, centerY, tolerance, bbox)
    let onCount = 0
    for (let i = 0; i < mask.length; i++) onCount += mask[i]
    const boxArea = (maxX - minX + 1) * (maxY - minY + 1)
    const ratio = onCount / boxArea

    if (ratio < 0.05 || ratio > 0.95) {
      return [
        { x: minX, y: minY },
        { x: maxX, y: minY },
        { x: maxX, y: maxY },
        { x: minX, y: maxY },
      ]
    }

    const contour = traceContour(mask, c.width, c.height)
    if (contour.length < 3) {
      return [
        { x: minX, y: minY },
        { x: maxX, y: minY },
        { x: maxX, y: maxY },
        { x: minX, y: maxY },
      ]
    }
    const simplified = douglasPeucker(contour, simplifyEpsilon)
    if (simplified.length < 3) return null
    return simplified.map(([x, y]) => ({ x, y }))
  }

  return { detecteer, detecteerInBox }
}
