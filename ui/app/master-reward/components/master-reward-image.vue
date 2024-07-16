<template>
        
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-card fill-height flat>
            
                            <v-card-text class="pa-0">
                                <v-card
                                    class="photo_box"
                                    id="photo_box"
                                    elevation="24"
                                >
                                    <div class="photo_inside" id="photo_inside" v-show="camera">
                                        asasdasd ads asd ad ad as da sd a das d sa das d ad as d as dsa ds ad asd sa das d asd as da sd sad as das das d asd sa das d sad as da sd
                                    </div>

                                    <div class="photo_inside" id="photo_inside_2" v-show="!camera">
                                        <v-img
                                        :src="imageUrl"
                                        aspect-ratio="1.34"
                                        class="grey lighten-2 elevation-2"
                                        contain
                                        >
                                        <!-- <img :src="imageUrl" height="150" v-if="imageUrl"/> -->
                                    </div>
                                </v-card>

                                <v-flex xs12 class="text-xs-center text-sm-center text-md-center text-lg-center">
                                    
                                    <v-text-field label="Pilih Gambar" hide-details @click='pickFile' v-model='imageName' prepend-icon='attach_file' class="mt-2"></v-text-field>
                                    <input
                                        type="file"
                                        style="display: none"
                                        ref="image"
                                        accept="image/*"
                                        @change="onFilePicked"
                                    >
                                </v-flex>

                                <!-- <v-flex xs12>
                                    <v-btn color="success" block @click="upload" :disabled="camera" :dark="!camera">Simpan</v-btn>
                                </v-flex> -->
                            </v-card-text>

                        </v-card>
                    </v-flex>
                    
                </v-layout>
</template>

<style>
.photo_box {
    overflow-x: hidden;
}

.photo_inside {
    /* position: absolute;
    top: 0;
    left: 0; */
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* #photo_inside_2 {
    min-height: 201px;
} */
</style>

<script>
module.exports = {
    data () {
        return {
            title: "Image Upload",
            // imageName: '',
            // imageUrl: '',
            // imageFile: '',
            camera: false
        }
    },

    computed : {
        imageName : {
            get () { return this.$store.state.image.imageName },
            set (v) { this.$store.commit('image/setImageName', v) }
        },

        imageUrl : {
            get () { return this.$store.state.image.imageUrl },
            set (v) { this.$store.commit('image/setImageUrl', v) }
        },

        imageFile : {
            get () { return this.$store.state.image.imageFile },
            set (v) { this.$store.commit('image/setImageFile', v) }
        }
    },

    methods : {
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
                })
                
                
			} else {
				this.imageName = ''
				this.imageFile = ''
				this.imageUrl = ''
			}
        },
        
        upload () {
            this.$store.commit('image/update_photo_64', this.imageUrl)
            this.$store.dispatch('image/upload')
            // this.$store.commit('profile/set_common', ['user_photo', this.imageUrl])
            // this.dialog = false
        }
    },

    watch : {

    }
}
</script>
