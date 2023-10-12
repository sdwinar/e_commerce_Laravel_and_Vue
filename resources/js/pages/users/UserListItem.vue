<script setup>
import { formatDate  } from "../../helper.js";
import { ref } from "vue";
import { useToastr } from "../../toastr.js";

const toastr = useToastr();

defineProps({
  user: Object,
  index: Number,
});


const roles = ref([
    {
        name: 'MANEGER',
        value: 0,
    },
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    }
]);
//**************************************************************** */
const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
    .then(() => {
        toastr.success('Role changed successfully!');
    })
};
//***************************************************************** */
const emit = defineEmits(['userDeleted' , 'editUser']);

// const userIdBeingDeleted = ref(null);
//******************************************************************** */
const confirmUserDeletion = (user) => {

   $('#user_id').val(user.id);
  $("#deleteUserModal").modal("show");
  //alert(userIdBeingDeleted.value)
  //alert(username.value)
  $('#exampleModalLabel').text('Delete'+' '+ user.name)
  $('#are_you_sure').text(' '+ user.name+' '+'?')
};
//******************************************************************** */
const deleteUser = () => {
    var userIdBeingDeleted = $('#user_id').val();
  axios.delete(`/api/users/${userIdBeingDeleted}`).then(() => {

    $("#deleteUserModal").modal("hide");

   // alert(userIdBeingDeleted)


  //  users.value = users.value.filter((user) => user.id !== userIdBeingDeleted.value);

    toastr.success("User deleted successfully!");
    emit('userDeleted', userIdBeingDeleted);

  });
};
//*********************************************************************************************************** */
</script>
<template>
  <tr   class="text-center">
    <th scope="row">{{ index + 1 }}</th>
    <td>{{ user.name }}</td>
    <td>{{ user.email }}</td>
    <td>{{ formatDate(user.created_at) }}</td>
    <td>
        <select  @change="changeRole(user, $event.target.value)" class="form-control text-center">
            <option v-for="role in roles" :selected="(user.role === role.name)"
            :value="role.value" :key="role.id">{{ role.name }}</option>
        </select>
    </td>
    <td>
      <a href="#" @click.prevent="$emit('editUser',user)"
        ><i class="fa fa-edit"></i>
      </a>
      <a href="#" @click.prevent="confirmUserDeletion(user)"
        ><i class="fa fa-trash ml-3 text-danger" aria-hidden="true"></i>
      </a>
    </td>
  </tr>

    <!-- Modal -->
    <input type="hidden" id="user_id" value="">
    <div class="modal fade" id="deleteUserModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <span >Are you sure you want to delete<p id="are_you_sure"
            style="color: red;font-weight: bold;
                margin-top: -24px;    margin-left: 215px; " ></p> </span>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            No
          </button>
          <button
            @click.prevent="deleteUser"
            type="button"
            class="btn btn-danger"
          >
            Yes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
