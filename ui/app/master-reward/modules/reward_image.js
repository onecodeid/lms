// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_reward.js"
import { one_token } from "../../assets/js/global.js"
window.api = api

export default  {
   namespaced: true,
   state: {
      dialog_photo: false,
      photo_64: '',
      photo_url: 'https://www.sgm-inc.com/wp-content/uploads/2014/06/no-profile-male-img.gif',
      default_photo_url: 'https://www.sgm-inc.com/wp-content/uploads/2014/06/no-profile-male-img.gif',
      patient_id: 0,
      imageName: '',
      imageUrl: '',
      imageFile: ''
   },
   mutations: {
      update_dialog_photo (state, v) {
          state.dialog_photo = v
      },

      update_photo_64 (state, data) {
          state.photo_64 = data
      },

      update_photo_url (state, data) {
          state.photo_url = data
      },

      setImageName (state, data) {
          state.imageName = data
      },

      setImageUrl (state, data) {
          state.imageUrl = data
      },

      setImageFile (state, data) {
          state.imageFile = data
      }
   },
   actions: {
        async upload(context) {
            context.commit('set_dialog_progress', true, {root:true})
            try {
            let resp = await api.upload_photo({
                token:one_token(), 
                customer_id:context.rootState.profile.customer_id, 
                photo:context.state.photo_64
            })
            context.commit('set_dialog_progress', false, {root:true})
            if (resp.status != "OK") {
                              
            } else {
                
                context.commit('profile/set_common', ['snackbar', true], {root:true})
                context.commit('profile/set_common', ['snackbar_text', 'Foto berhasil diupload dan diperbarui'], {root:true})
            }
            } catch(e) {
                context.commit('set_dialog_progress', false, {root:true})
                console.log(e)
            }
        }
   }
}
