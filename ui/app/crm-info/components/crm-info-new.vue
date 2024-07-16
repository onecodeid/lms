<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>PENGUMUMAN BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-card style="height:500px">
                                    <v-card-text class="watzap-text-container fill-height">
                                        <v-layout row wrap>
                                            <v-flex xs12 class="px-2">
                                                <v-text-field
                                                    label="Judul"
                                                    v-model="info_title"
                                                ></v-text-field>
                                            </v-flex>
                                        </v-layout>
                                        
                                        <v-layout column>
                                            <v-flex>
                                                <v-textarea
                                                    v-model="info_content"
                                                    class="fill-height pa-2"

                                                    label="Konten"
                                                >
                                                </v-textarea>
                                            </v-flex>
                                        </v-layout>

                                        <v-layout row wrap>
                                            <v-flex xs6 pl-2>
                                                <common-datepicker
                                                    label="Tanggal berlaku"
                                                    :date="sdate"
                                                    data="0"
                                                    @change="change_sdate"
                                                    classs="mt-0 input-dense"
                                                    hints=""
                                                    :details="false"
                                                    :solo="false"
                                                    v-if="dialog"
                                                ></common-datepicker>
                                            </v-flex>

                                            <v-flex xs6 pr-2>
                                                <common-datepicker
                                                    label="Sampai Tanggal"
                                                    :date="edate"
                                                    data="0"
                                                    @change="change_edate"
                                                    classs="mt-0 ml-1 input-dense"
                                                    hints=""
                                                    :details="false"
                                                    :solo="false"
                                                    v-if="dialog"
                                                ></common-datepicker>
                                            </v-flex>
                                        </v-layout>

                                        <v-layout row wrap>
                                            <v-flex xs12 v-if="(info_img==null||info_img=='')&&!!dialog">
                                                <v-divider class="mt-5 mb-2"></v-divider>
                                                <v-text-field label="Select Image" @click='pickFile' v-model='imageName' prepend-icon='attach_file'></v-text-field>
                                                <input
                                                    type="file"
                                                    style="display: none"
                                                    ref="image"
                                                    accept="image/*"
                                                    @change="onFilePicked"
                                                >
                                                <v-divider class="mt-2 mb-0"></v-divider>
                                            </v-flex>
                                        </v-layout>

                                        

                                        <v-layout row wrap>
                                            <v-flex xs12 style="position:relative" v-show="info_img!=null&&info_img!=''">
                                                <v-img :src="info_img" position="center center" class="ma-0">
                                                </v-img>
                                                <div style="height:50px;background:grey;position:absolute;top:0;width:100%">
                                                    <v-btn color="red" class="btn-icon" @click="clearImage">close</v-btn>
                                                </div>
                                            </v-flex>
                                        </v-layout>

                                        

                                    </v-card-text>
                                </v-card>
                                
                            </v-flex>
                            
                        </v-layout>
                    </v-flex>
                    
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.watzap-text-container {
    /* background-image:url(../../assets/img/bg-whatsapp.png); */
    font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", 
  Helvetica, Arial, "Lucida Grande", sans-serif;
}

.watzap-text-container textarea {
    overflow: auto;
}

.watzap-text-container textarea::-webkit-scrollbar {
    width: .3em;
}

/* .watzap-text-container textarea::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}

.watzap-text-container textarea::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
} */
</style>
<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            shortcodes: this.$store.state.info.shortcodes,
            imageName: '',
            imageUrl: '',
            imageFile: ''
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.info_new.dialog_new },
            set (v) { this.$store.commit('info_new/set_common', ['dialog_new', v]) }
        },

        info_title : {
            get () { return this.$store.state.info_new.info_title },
            set (v) { this.$store.commit('info_new/set_common', ['info_title', v]) }
        },

        info_content : {
            get () { return this.$store.state.info_new.info_content },
            set (v) { this.$store.commit('info_new/set_content', v) }
        },

        colors () {
            return this.$store.state.info_new.colors
        },

        selected_color : {
            get () { return this.$store.state.info_new.selected_color },
            set (v) { this.$store.commit('info_new/set_object', ['selected_color', v]) }
        },

        info_img : {
            get () {
                if (this.$store.state.info_new.info_img)
                    return this.$store.state.info_new.info_img
                return ''
            },
            set (v) { this.$store.commit('info_new/set_common', ['info_img', v]) }
        },

        edate : {
            get () { return this.$store.state.info_new.info_edate },
            set (v) { this.$store.commit('info_new/set_common', ['info_edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.info_new.info_sdate },
            set (v) { this.$store.commit('info_new/set_common', ['info_sdate', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('info_new/save')
        },

        insertAtCursor (x) {
            let t = this.$el.querySelector('.v-input__control')
            console.log(t)
        },

        pickFile () {
            this.$refs.image.click ()
        },

        onFilePicked (e) {

            const files = e.target.files
            if(files[0] !== undefined) {
                this.imageName = files[0].name
                
                if(this.imageName.lastIndexOf('.') <= 0) {
                    return
                }
                const fr = new FileReader ()
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.imageUrl = fr.result
                    this.imageFile = files[0] // this is an image file that can be sent to server...
                    this.getBase64(files[0])
                })
            } else {
                this.imageName = ''
                this.imageFile = ''
                this.imageUrl = ''
            }
        },
        
        getBase64(file) {
            let vue = this
            let photo_uri = ''
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
            //   console.log(reader.result);
                photo_uri = reader.result
                vue.info_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        },

        clearImage() {
            this.imageUrl = ''
            this.imageName = ''
            this.imageFile = ''
            this.info_img = null
        },

        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        }
    },

    mounted () {
        this.$store.dispatch('info_new/search_color')
    },

    watch : {}
}
</script>