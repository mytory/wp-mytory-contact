<template>
    <transition name="modal">
        <div @click="$emit('user-closed-modal')" class="modal-mask">
            <div class="modal-wrapper">
                <div @click.stop class="modal-container">
                    <div class="modal-body">
                        <button @click="$emit('close')" class="c-close  c-close--absolute">&times;</button>
                        {{ group.name }}
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: "group-manager",
        data() {
            return {}
        },
        props: ['group'],
        mounted() {
        }
    }
</script>
