<template>
    <transition name="fade">
        <div v-if="isOpen" @click="close" v-click-outside="overridableToggle">
            <slot />
        </div>
    </transition>
</template>

<script>
export default {
    name: "BaseDropdown",

    props: [
        'parent_overridden'
    ],

    data: function() {
        return {
            open: false
        };
    },

    computed: {
        isOpen: {
            get: function() {
                return this.open
            },
            set: function(val) {
                this.open = val;
            }
        }
    },

    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        },

        overridableToggle(e) {
            if (!this.parent_overridden) {
                this.close();
            } else {
                if (!(e.target == this.$parent.$el || this.$parent.$el.contains(e.target))) {
                    return this.close();
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>

.fade-enter-active, .fade-leave-active {
    transition: opacity .2s;
}
.fade-enter, .fade-leave-active {
    opacity: 0;
}
</style>
