<script setup>
defineProps({
  artikel: { type: Object, required: true },
})

defineEmits(['select'])
</script>

<template>
  <div
    class="point-wrapper"
    :style="{ top: `${artikel.y}px`, left: `${artikel.x}px`, position: 'absolute' }"
    :data-catalogus="artikel.catalogusnummer"
    @click.stop="$emit('select', artikel)"
  >
    <i></i>
  </div>
</template>
