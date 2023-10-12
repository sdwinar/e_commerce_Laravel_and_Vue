<script setup>
import axios from 'axios';
import { ref, onMounted, reactive, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './UserListItem.vue';
// import { debounce } from 'lodash';
// import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const users = ref({'data': []});
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
    .then((response) => {
        users.value = response.data;
        selectedUsers.value = [];
        selectAll.value = false;
    })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
});

const createUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success('User created successfully!');
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
};

const addUser = () => {
    editing.value = false;
    $('#userFormModal').modal('show');
};

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = (values, { setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
}

const handleSubmit = (values, actions) => {
    // console.log(actions);
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}

// const searchQuery = ref(null);

// const selectedUsers = ref([]);
// const toggleSelection = (user) => {
//     const index = selectedUsers.value.indexOf(user.id);
//     if (index === -1) {
//         selectedUsers.value.push(user.id);
//     } else {
//         selectedUsers.value.splice(index, 1);
//     }
//     console.log(selectedUsers.value);
// };

const userIdBeingDeleted = ref(null);
const confirmUserDeletion = (id) => {
    userIdBeingDeleted.value = id;
    $('#deleteUserModal').modal('show');
};

const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
    .then(() => {
        $('#deleteUserModal').modal('hide');
        toastr.success('User deleted successfully!');
        users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value);
    });
};

// const bulkDelete = () => {
//     axios.delete('/api/users', {
//         data: {
//             ids: selectedUsers.value
//         }
//     })
//     .then(response => {
//         users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
//         selectedUsers.value = [];
//         selectAll.value = false;
//         toastr.success(response.data.message);
//     });
// };

// const selectAll = ref(false);
// const selectAllUsers = () => {
//     if (selectAll.value) {
//         selectedUsers.value = users.value.data.map(user => user.id);
//     } else {
//         selectedUsers.value = [];
//     }
//     console.log(selectedUsers.value);
// }

// watch(searchQuery, debounce(() => {
//     getUsers();
// }, 300));

onMounted(() => {
    getUsers();
});
</script>
<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">USERS</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <router-link to="/admin">Home</router-link>
            </li>
            <li class="breadcrumb-item active">Users</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <button
          type="button"
          class="btn btn-primary mb-2"
          data-toggle="modal"
          data-target="#userFormModal"
          @click="addUser"
        >
          Add New User
        </button>
      </div>
      <div class="row">
        <table class="table table-bordered">
          <thead>
            <tr class="text-center">
              <th scope="col">#</th>
              <th scope="col">name</th>
              <th scope="col">email</th>
              <th scope="col">created at</th>
              <th scope="col">Role</th>
              <th scope="col">Handle</th>
            </tr>
          </thead>
          <tbody>

         <UserListItem  v-for="(user, index) in users"
          :key="user.id"
          :user=user
          :index=index
          @user-deleted="userDeleted"

          />

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div
    class="modal fade"
    id="userFormModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <Form
        ref="form"
        autocomplete="off"
        @submit="handleSubmit"
        :validation-schema="editing ? editUserSchema : createUserSchema"
        v-slot="{ errors }"
        :initial-values="formValues"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">
              <span v-if="editing">Edit User</span>
              <span v-else>Add New User</span>
            </h5>
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
            <div class="form-group">
              <label for="name">Name</label>
              <Field
                :class="{ 'is-invalid': errors.name }"
                name="name"
                type="text"
                class="form-control"
                id="name"
                aria-describedby="nameHelp"
                placeholder="Enter full name"
              />
              <span class="invalid-feedback">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <Field
                :class="{ 'is-invalid': errors.email }"
                name="email"
                type="email"
                class="form-control"
                id="email"
                aria-describedby="nameHelp"
                placeholder="Enter full name"
              />
              <span class="invalid-feedback">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <Field
                :class="{ 'is-invalid': errors.password }"
                name="password"
                type="password"
                class="form-control"
                id="password"
                aria-describedby="nameHelp"
                placeholder="Enter password"
              />
              <span class="invalid-feedback">{{ errors.password }}</span>
            </div>
          </div>
          <div class="modal-footer">
            <!-- <button
              type="button"
              class="btn btn-warning"
              onclick="reset()"
            >
              Re Set Form
            </button> -->
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
              @click="cancel_button_resetform"
            >
              Cancel
            </button>

            <button type="submit" class="btn btn-primary">
              {{ editing ? "Edit" : "Save" }}
            </button>
          </div>
        </div>
      </Form>
    </div>
  </div>


</template>
