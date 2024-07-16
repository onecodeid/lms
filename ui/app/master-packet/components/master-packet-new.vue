<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title>
                <h3>PAKET BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs7>
                                <v-layout row wrap>
                                    <v-flex xs12 mt-3>
                                        <v-text-field
                                            label="Kode Paket"
                                            hide-details
                                            v-model="packet_code"
                                        ></v-text-field>
                                    </v-flex>

                                    <v-flex xs12 :class="`mt-`+v_space">
                                        <v-text-field
                                            label="Nama Paket"
                                            hide-details
                                            v-model="packet_name"
                                        ></v-text-field>
                                    </v-flex>

                                    <v-flex xs12 :class="`mt-`+v_space" v-show="!edit">
                                        <v-checkbox
                                            label="Publish ?"
                                            hide-details
                                            v-model="packet_publish"
                                            true-value="Y"
                                            false-value="N"
                                        ></v-checkbox>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs5>
                                <v-layout row wrap>
                                    <v-flex xs12 align-center
                justify-center offset-xs3>
                                        <v-img :src="packet_img" position="center center" class="ma-0" style="min-height:140px">
                                        </v-img>
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
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="publish('Y')" v-show="edit && packet_publish=='N'">Publish</v-btn>
                <v-btn color="red" dark @click="publish('N')" v-show="edit && packet_publish=='Y'">UnPublish</v-btn>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    data () {
        return {
            imageName: '',
            imageUrl: '',
            imageFile: ''
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.packet_new.dialog_new },
            set (v) { this.$store.commit('packet_new/set_common', ['dialog_new', v]) }
        },

        packet_name : {
            get () { return this.$store.state.packet_new.packet_name },
            set (v) { this.$store.commit('packet_new/set_common', ['packet_name', v]) }
        },

        packet_code : {
            get () { return this.$store.state.packet_new.packet_code },
            set (v) { this.$store.commit('packet_new/set_common', ['packet_code', v]) }
        },

        packet_publish : {
            get () { return this.$store.state.packet_new.packet_publish },
            set (v) { this.$store.commit('packet_new/set_common', ['packet_publish', v]) }
        },

        v_space () {
            return 3
        },

        edit () {
            return this.$store.state.packet_new.edit
        },

        packet_img : {
            get () {
                if (this.$store.state.packet_new.packet_img)
                    return this.$store.state.packet_new.packet_img
                return ''
            },
            set (v) { this.$store.commit('packet_new/set_common', ['packet_img', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('packet_new/save')
        },

        publish (x) {
            this.$store.commit('packet_new/set_common', ['packet_publish', x])
            this.save()
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
                photo_uri = reader.result
                vue.packet_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        }
    },

    mounted () {
        
    },

    watch : {
        
    }
}
</script>