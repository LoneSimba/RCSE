<template>
  <div class="dropdown-wrapper">
      <div class="parent-contents" @click="$refs[dropdown].toggle()">
          <slot name="parent"></slot>
      </div>
      <BaseDropdown class="dropdown-contents"
                    :ref="this.dropdown"
                    :parent_overridden="true">
          <slot name="dropdown"></slot>
      </BaseDropdown>
  </div>
</template>

<script>
import BaseDropdown from "./BaseDropdown";

export default {
    name: "NestedDropdown",

    components: {BaseDropdown},

    data: function() {
        return {
            dropdown: 'dropdown' + Math.random()
        };
    }
}
</script>

<style scoped>

</style>
