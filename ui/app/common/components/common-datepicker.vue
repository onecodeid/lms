<template>
    <v-menu
          v-model="menu2"
          :close-on-content-click="false"
          :nudge-right="40"
          lazy
          transition="scale-transition"
          offset-y
          full-width
          max-width="290px"
          min-width="290px"
        >
          <v-text-field
            slot="activator"
            v-model="computedDateFormatted"
            :label="init_label"
            :hint="init_hint"
            persistent-hint
            readonly
            :solo="init_solo"
            :hide-details="!init_detail"
            :class="init_class"
          ></v-text-field>
          <v-date-picker v-model="init_date" no-title @input="menu2 = false"></v-date-picker>
    </v-menu>
 </template>

 <script>
 module.exports = {
     props : ['label', 'date', 'data', 'classs', 'details', 'hints', 'solo'],

     data () {
        return {
            init_date: this.date ? this.date : new Date().toISOString().substr(0, 10),
            dateFormatted: this.formatDate(new Date().toISOString().substr(0, 10)),
            menu1: false,
            menu2: false,

            init_label: this.label ? this.label : 'Date',
            init_data: this.data ? this.data : '',
            init_class: this.classs ? this.classs : '',
            init_detail: this.details ? this.details : false,
            init_hint: this.hints ? this.hints : 'DD-MM-YYYY format',
            init_solo: this.solo == null ? true : this.solo
        }
     },

    computed: {
      computedDateFormatted () {
        return this.formatDate(this.init_date)
      }
    },

    watch: {
      init_date (n, o) {
        this.dateFormatted = this.formatDate(this.init_date)
        
        this.$emit('change', {"old_date":o, "new_date":n, "data":this.init_data});
      }
    },

    methods: {
      formatDate (date) {
        if (!date) return null

        const [year, month, day] = date.split('-')
        return `${day}-${month}-${year}`
      },
      parseDate (date) {
        if (!date) return null

        const [month, day, year] = date.split('/')
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
      },

      emitChange (n, o) {
          console.log("old:"+o)
          console.log("new:"+n)
      }
    },

    mounted () {
        
    }
 }
</script>
