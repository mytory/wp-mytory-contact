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

                        <div class="u-text-center" v-show="isProcessing">
                            <div class="lds-roller">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>


                        <table class="form-table" v-show="!isProcessing">
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

    import axios from "axios";
    import qs from "qs";

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
                this.isProcessing = true;
                this.myContact.phone = this.myContact.phone.replace(/[^0-9]/g, '');
                axios({
                    method: 'POST',
                    url: ajaxurl,
                    headers: {'content-type': 'application/x-www-form-urlencoded'},
                    data: qs.stringify({contact: this.contact, action: 'mytory_contact_save'})
                }).then(res => {
                    this.isProcessing = false;
                    if (res.data.result === 'success') {
                        this.myContact = res.data.contact;
                        this.close();
                    } else {
                        throw res.data;
                    }
                }).catch(error => {
                    swal("문제가 발생했습니다", error.message, error.result);
                });
            }
        },
        props: ['contact']
    }
</script>

