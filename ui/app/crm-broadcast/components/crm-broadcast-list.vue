<template>
    <v-card flat class="fill-height">
        <v-card-text class="fill-height" style="overflow-y:scroll">
            <v-layout row wrap>
                <template v-for="(rec, n) in recipients" :key="n">
                    <v-flex xs12 v-show="n%3==0&&n>0">
                        <v-divider class="my-2"></v-divider>
                    </v-flex>
                    <v-flex xs4>
                        {{ rec.no }} 
                        <v-icon small class="green--text" v-show="recipients_index[rec.no].status=='200'">check_circle</v-icon> <br>
                        <span class="caption" v-show="!recipients_index[rec.no].status">{{ rec.name }}</span>
                        <span class="caption orange--text" v-show="recipients_index[rec.no].status=='100'">Mengirim ...</span>
                        <span class="caption green--text" v-show="recipients_index[rec.no].status=='200'">{{ rec.name }}</span>
                        <span class="caption red--text" v-show="recipients_index[rec.no].status=='1005'">
                            <v-icon small class="red--text">info</v-icon> Gagal Mengirim</span>
                    </v-flex>
                </template>
                
            </v-layout>
            
        </v-card-text>

        <!-- <v-snackbar
            v-model="snackbar"
            :timeout=3000
            top
            >
            {{snacktext}}
            <v-btn
                color="pink"
                flat
                @click="snackbar = false"
            >
                Tutup
            </v-btn>
        </v-snackbar> -->
    </v-card>
</template>

<script>
module.exports = {
    components : {

    },

    data () {
        return {
            
        }
    },

    computed : {
        recipients () {
            return this.$store.state.broadcast.recipients
        },

        recipients_index () {
            return this.$store.state.broadcast.recipients_index
        }
    },

    methods: {
        
    },

    mounted () {
        console.log(this.$store.state.broadcast.one_user)
    }
}
</script>