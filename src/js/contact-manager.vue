<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <header class="modal-header">
                        <h1 style="float: left; padding: 0;">{{ myContact.name }}</h1>

                        <div style="float: right;">
                            <button class="button  button-primary" @click="save()" :disabled="isProcessing">
                                저장
                            </button>
                            <button class="button" @click="close" :disabled="isProcessing">
                                취소
                            </button>
                        </div>
                    </header>

                    <div class="modal-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="name">이름</label></th>
                                <td><input type="text" name="name" id="name" v-model="myContact.name"></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="phone">전화번호</label></th>
                                <td><input type="text" name="phone" id="phone" v-model="myContact.phone"></td>
                            </tr>
                        </table>
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
            return {
                isProcessing: false,
                myContact: this.contact,
            }
        },
        methods: {
            close() {
                this.$emit('close');
            },
            save() {
                swal('save');
            }
        },
        props: ['contact']
    }
</script>

