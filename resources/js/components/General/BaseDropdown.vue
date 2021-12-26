<template>
        <div v-if="isOpen" @click="toggle" v-click-outside="overridableToggle">
            <transition name="fade">
                <slot />
            </transition>
        </div>
</template>

<script>
export default {
    name: "BaseDropdown",

    props: [
        'override_click_outside',
        'parent'
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

        overridableToggle(e) {
            let parent = this.$props.parent;
            if (
                this.override_click_outside === undefined ||
                this.override_click_outside === false ||
                parent === undefined
            ) {
                this.toggle();
            }

            if (!(e.target == parent || parent.contains(e.target))) {
                return this.toggle();
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
