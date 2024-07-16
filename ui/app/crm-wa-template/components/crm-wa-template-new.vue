<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="800px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>EXPENSE BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs6 pr-5>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-text-field
                                            label="Nama Template"
                                            v-model="watemplate_name"
                                        ></v-text-field>
                                    </v-flex>

                                    <v-flex xs12>
                                        <v-select
                                            :items="colors"
                                            v-model="selected_color"
                                            item-value="color_id"
                                            item-text="color_name"
                                            return-object
                                            label="Warna"
                                        >
                                            <template slot="item" slot-scope="data">
                                                <v-chip small :color="data.item.color_class" bloc :text-color="data.item.color_text_class">{{data.item.color_name}}</v-chip>
                                            </template>
                                            <template slot="selection" slot-scope="data">
                                                <v-chip small :color="data.item.color_class" bloc :text-color="data.item.color_text_class">{{data.item.color_name}}</v-chip>
                                            </template>
                                        </v-select>
                                    </v-flex>

                                    

                                    
                                    
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-flex>

                    <v-flex xs6>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-card style="height:500px">
                                    <v-card-text class="watzap-text-container fill-height">
                                        <v-layout row wrap>
                                            <v-flex xs12 v-if="(watemplate_img==null||watemplate_img=='')&&!!dialog">
                                                <v-text-field label="Select Image" @click='pickFile' v-model='imageName' prepend-icon='attach_file'></v-text-field>
                                                <input
                                                    type="file"
                                                    style="display: none"
                                                    ref="image"
                                                    accept="image/*"
                                                    @change="onFilePicked"
                                                >
                                            </v-flex>
                                        </v-layout>
                                        <v-layout row wrap>
                                            <v-flex xs12 style="position:relative" v-show="watemplate_img!=null&&watemplate_img!=''">
                                                <v-img :src="watemplate_img" position="center center" class="ma-0">
                                                </v-img>
                                                <div style="height:50px;background:grey;position:absolute;top:0;width:100%">
                                                    <v-btn color="red" class="btn-icon" @click="clearImage">close</v-btn>
                                                </div>
                                            </v-flex>
                                        </v-layout>
                                        <v-layout column>
                                            
                                            <v-flex>
                                                <v-textarea
                                                    v-model="watemplate_content"
                                                    class="fill-height pa-2"
                                                    hide-details
                                                >
                                                </v-textarea>
                                            </v-flex>
                                        </v-layout>
                                       
                                    </v-card-text>
                                </v-card>
                                
                            </v-flex>
                            <v-flex xs12>
                                <v-chip v-for="(sc, n) in shortcodes" :key="n" @click="insertAtCursor(sc)">[{{sc}}]</v-chip>
                                <!-- <v-chip @click="insertAtCursor()">[customer_name]</v-chip>
                                <v-chip>[customer_phone]</v-chip>
                                <v-chip>[admin_name]</v-chip>
                                <v-chip>[admin_phone]</v-chip> -->
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
    background-image:url(../../assets/img/bg-whatsapp.png);
    font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", 
  Helvetica, Arial, "Lucida Grande", sans-serif;
}

.watzap-text-container textarea {
    text-align: right;
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
            shortcodes: this.$store.state.watemplate.shortcodes,
            imageName: '',
            imageUrl: '',
            imageFile: ''
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.watemplate_new.dialog_new },
            set (v) { this.$store.commit('watemplate_new/set_common', ['dialog_new', v]) }
        },

        watemplate_name : {
            get () { return this.$store.state.watemplate_new.watemplate_name },
            set (v) { this.$store.commit('watemplate_new/set_common', ['watemplate_name', v]) }
        },

        watemplate_content : {
            get () { return this.$store.state.watemplate_new.watemplate_content },
            set (v) { this.$store.commit('watemplate_new/set_content', v) }
        },

        colors () {
            return this.$store.state.watemplate_new.colors
        },

        selected_color : {
            get () { return this.$store.state.watemplate_new.selected_color },
            set (v) { this.$store.commit('watemplate_new/set_object', ['selected_color', v]) }
        },

        watemplate_img : {
            get () {
                if (this.$store.state.watemplate_new.watemplate_img)
                    return this.$store.state.watemplate_new.watemplate_img
                return ''
            },
            set (v) { this.$store.commit('watemplate_new/set_common', ['watemplate_img', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('watemplate_new/save')
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
                vue.watemplate_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        },

        clearImage() {
            this.imageUrl = ''
            this.imageName = ''
            this.imageFile = ''
            this.watemplate_img = null
        }
    },

    mounted () {
        this.$store.dispatch('watemplate_new/search_color')
    },

    watch : {}
}
</script>