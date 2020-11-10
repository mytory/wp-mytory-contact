<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <header class="modal-header">

                        <h1>{{ group.name }}</h1>

                        <div style="float: right;">
                            <button class="button  button-primary" @click="save()"
                                    :disabled="isProcessing">
                                저장
                            </button>
                            <button class="button" @click="close"
                                    :disabled="isProcessing">
                                취소
                            </button>
                        </div>

                    </header>

                    <div class="modal-body">
                        <div style="text-align: center;">
                            <button class="button  button-primary" @click="save()" :disabled="isProcessing">
                                저장
                            </button>
                            <button class="c-btn  c-btn--danger" @click="close" :disabled="isProcessing">
                                취소
                            </button>
                        </div>
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
                isProcessing: false
            }
        },
        methods: {
            save() {
                this.isProcessing = true;
                swal('저장.').then(() => {
                    this.$emit('close');
                });
            },
            close() {
                swal('저장하지 않은 데이터가 날아갑니다. 취소하고 닫을까요?', {
                    buttons: {
                        open: '열어 두기',
                        close: {
                            'text': '닫기',
                            'value': 'close',
                            className: 'swal-button swal-button--confirm swal-button--danger'
                        }
                    }
                }).then(data => {
                    console.log(data);
                    switch (data) {
                        case 'close':
                            this.$emit('close');
                            break;
                        case 'open':
                            break;
                    }
                }).catch(error => {
                    console.log(error);
                });

            }
        },
        props: ['group'],
    }
</script>

