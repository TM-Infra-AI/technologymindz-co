<?php

session_start();


require 'helper/helper.php';
require_once '../includes/config.php';
include_once('../db_config.php');



// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// if (!$connect) {

//     if ($_SERVER['HTTP_HOST'] == "localhost") {
//         die("Database Connection Failed: " . mysqli_connect_error());
//     } else {
//         die("Database connection issue. Please contact support.");
//     }
// }

$user_email = $_SESSION['current_email'];
$user_role = getUserRole($user_email);

if ($user_role !== 'admin') {
    header("Location: " . BASE_URL . "/panel/login/login.php");
    exit;
}

$query = "SELECT * FROM digi_card";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="fav-icon" href="images/logo.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#userTable').DataTable({
                dom: `
                    <"top d-flex justify-content-between align-items-center mb-3 flex-nowrap"
                        <"me-3" l>
                        <"flex-grow-1 d-flex justify-content-center" f>
                        <"ms-3" p>
                    >
                    t
                `,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search users..."
                },
                columnDefs: [
                    { targets: [8, 9], orderable: false } // Disable sorting for User URL and Action columns
                ]
            });
        });
    </script>

    <style>
        .dataTables_filter input {
            max-width: 300px;
        }

        table>tbody>tr:hover {
            filter: drop-shadow(0px 2px 6px #0002);
        }

        .dataTables_wrapper table td {
            padding: 15px 15px !important;
            border-bottom-width: 0px !important;
            border: 0 !important;
        }

        .container table th {
            padding: 15px 15px !important;
            background-color: #013388;
            color: #fff !important;
            border-bottom-width: 0px !important;
            border: 0 !important;
        }

        .container table {
            border-radius: 10px !important;
            border: 0px !important;
            box-shadow: 0 0px 40px 0px rgba(0, 0, 0, 0.15);
            -moz-box-shadow: 0 0px 40px 0px rgba(0, 0, 0, 0.15);
        }

        button.btn.btn-primary.btn-sm.edit-btn {
            border-color: #26a69a;
            text-decoration: none;
            color: #444;
            background: #fff;
            border: 1px solid;
            display: inline-block;
            padding: 12px 20px;
            font-weight: bold;
            border-radius: 3px;
            transition: 0.3s ease-in-out;
        }

        button.btn.btn-danger.btn-sm.delete-btn {
            border-color: #dc3545 !important;
            text-decoration: none;
            color: #444;
            background: #fff;
            border: 1px solid;
            display: inline-block;
            padding: 7px 20px;
            font-weight: bold;
            border-radius: 3px;
            transition: 0.3s ease-in-out;
        }

        th.sorting.sorting_desc:after,
        th.sorting.sorting_desc:before {
            color: #f5f5f5;
            margin-left: 10px;
        }

        th.sorting:after,
        th.sorting:before {
            margin-left: 10px;
            right: 0px ! IMPORTANT;
        }

        th.sorting.sorting_asc:after,
        th.sorting.sorting_asc:before {
            color: #fff;
            margin-left: 10px;
        }

        button.btn.btn-primary.btn-sm.edit-btn:hover,
        button.btn.btn-danger.btn-sm.delete-btn:hover {
            box-shadow: 0 3px 8px #0003;
        }

        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .pagination {
            margin: 20px 0 !important;
        }

        .page-item:last-child .page-link {
            background-color: #013388;
            color: #fff;
            border-radius: 4px;
            padding: 6px 20px;
        }

        .active>.page-link,
        .page-link.active {
            background-color: #013388;
            color: #fff;
        }

        a.page-link {
            padding: 6px 15px;
            border-radius: 6px;
        }

        li.paginate_button.page-item {
            margin-right: 5px !important;
        }

        .page-link:focus {
            box-shadow: unset;
        }

        .modal-dialog {
            width: 100% !important;
        }

        .modal-body form .btn {
            background-color: #013388;
        }

        .modal-dialog {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .modal-content {
            width: 50%;
            margin: 0 auto;
        }

        .close {
            color: #878787;
            font-size: 17px;
            background: #f2f3fb;
            height: 33px;
            width: 33px;
            cursor: pointer;
            display: flex;
            align-items: center;
            border-radius: 50%;
            justify-content: center;
            transition: all 0.3s ease-in-out;
        }

        .modal-header .btn-close {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
        }

        .userForm button.btn.btn-primary.w-100 {
            padding: 10px;
            background-image: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            position: relative;
            overflow: hidden;
        }

        input.form-control {
            width: 100%;
            padding: 10px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
            background: #F9FAFB;
        }

        .modal-content label {
            display: block;
            margin-bottom: 8px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }

        div.dataTables_wrapper div.dataTables_filter label {
            text-align: right;
        }

        .form-control:focus {
            box-shadow: unset !important;
            border-radius: 5px !important;
        }

        select:focus {
            box-shadow: unset !important;
        }

        th.sorting.sorting_desc:after,
        th.sorting.sorting_asc:before {
            border-color: #fff !important;
            opacity: 1 !important;
        }

        th.sorting.sorting_desc:before,
        th.sorting.sorting_asc:after {
            border-color: #fff !important;
            opacity: .5 !important;
        }

        .dropdown-menu {
            min-width: 200px;
            background-color: white;
            z-index: 1000;
        }

        .dropdown-item {
            padding: 10px 15px;
            display: block;
            color: #333;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .top-nav{
            background: #015fb1;
            color: white;
            width: -webkit-fill-available;
            padding: 8px 40px !important;
            position: sticky;
            top: 0;
            z-index: 22;
            box-shadow: 0px 0px 10px 0px #00000026;
            align-items: center;
        }
        .top-nav .logo img {
            width: auto;
            height: 65px;
            margin: 10px;
        }
    </style>
    
</head>

<body>
    <!-- header -->
    <div class="top-nav">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="logo" onclick="location.href='index.php'">                
            <a href="<?php echo BASE_URL . '/panel/login/index.php'; ?>"></a><img src="images/logo.png"></a> <!--<h3>Customer Login</h3>-->
        </div>
        <h3 class="text-center m-0">User Management</h1>

            <!-- Right aligned My Account Dropdown -->
            <div class="ms-auto position-relative">
                <button class="btn dropdown-toggle" id="accountDropdownBtn" onclick="toggleAccountDropdown()">
                    <i class="fa fa-user"></i> 
                        <?php
                            if (isset($_SESSION['first_name'])) {
                                echo 'Hi ' . $_SESSION['first_name'] . '!';
                            } else {
                                echo 'Hi Guest!';
                            }
                        ?>
                </button>
                <div id="accountDropdownMenu" class="dropdown-menu dropdown-menu-end shadow" style="display: none; position: absolute; right: 0;">                    
                    <a class="dropdown-item" href="<?php echo BASE_URL . '/panel/login/index.php'; ?>">
                        <i class="fa fa-edit"></i> Profile
                    </a>
                    <a class="dropdown-item" href="<?php echo BASE_URL . '/panel/login/leads.php'; ?>">
                        <i class="fa fa-users"></i> Leads
                    </a>            
                    <a class="dropdown-item" href="<?php echo BASE_URL . '/panel/login/my_account.php'; ?>">                    
                        <i class="fa fa-lock"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div id="successAlert" class="alert alert-success" style="display: none; position: fixed; top: 10px; right: 10px; z-index: 9999;">
            <span id="successMessage"></span>
        </div>

        <div id="errorAlert" class="alert alert-danger" style="display: none; position: fixed; top: 10px; right: 10px; z-index: 9999;">
            <span id="errorMessage"></span>
        </div>

        <table id="userTable" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>User Name</th>
                    <th>Full Name</th>
                    <th>Active</th>
                    <th>Service</th>
                    <th>User Role</th>
                    <th>User URL</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                    "://" . $_SERVER['HTTP_HOST'];
                $i = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $row['user_email']; ?></td>
                        <td><?php echo $row['d_country_code'].'-'.$row['user_contact']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['d_f_name'] . ' ' . $row['d_l_name']; ?></td>
                        <td><?php echo $row['user_active']; ?></td>
                        <td><?php echo $row['select_service']; ?></td>
                        <td><?php echo $row['user_role'] ? ucfirst($row['user_role'])  : '-'; ?></td>
                        <td>
                            <a href="<?php echo $baseUrl . '/' . $row['card_id']; ?>" target="_blank" rel="noopener noreferrer">
                                View URL
                            </a>
                        </td>
                        <td style="display:flex;justify-content: space-between;gap: 5px;">
                            <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $row['id']; ?>">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button class="btn btn-success m-3" data-bs-toggle="modal" data-bs-target="#addUserModal" style="float:right">Add User</button>
    </div>

    <!-- Add/Edit User Modal -->

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit User</h5>
                    <div class="close"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                </div>

                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" name="id" id="user_id">
                        <input type="hidden" name="old_email" id="old_email">
                        <div class="mb-2">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="user_email" placeholder="abc@gmail.com" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Contact <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="text" name="country_code" class="form-control" placeholder="+91" style="max-width: 100px;" required>
                                <input type="text" name="phone_number" class="form-control" placeholder="9876543210" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>User Name <span class="text-danger">*</span></label>
                            <input type="text" name="user_name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="user_password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="user_password" name="user_password" required>
                        </div>

                        <div class="mb-2">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" required>
                            <small id="password-warning" style="color: orange; display: none;">Passwords do not match!</small>
                        </div>

                        <p id="password-warning" style="color:red; display:none;">Passwords does not match!</p>

                        <div class="mb-2">
                            <label>Active <span class="text-danger">*</span></label>
                            <select name="user_active" id="user-active" class="form-control" required>
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                            <!-- <input type="text" name="user_active" class="form-control"> -->
                        </div>

                        <div class="mb-2">
                            <label>User Role <span class="text-danger">*</span></label>
                            <select name="user_role" id="user-role" class="form-control">
                                <option value="user">User</option>                        
                                <option value="admin">Admin</option>
                            </select>

                            <!-- <input type="text" name="user_active" class="form-control"> -->
                        </div>

                        <div class="mb-2">
                            <label>Service</label>
                            <input type="text" name="select_service" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.edit-btn', function() {
            var userId = $(this).data('id');

            $.post('get-user.php', {
                id: userId
            }, function(response) {

                if (response.status === "success") {
                    let fullContact = response.data.user_contact; // e.g., "+91 9876543210" or "+919876543210"
                    let countryCode = '';
                    let phoneNumber = '';

                    if (fullContact.startsWith('+')) {
                        // Split by space or extract code and number manually
                        let match = fullContact.match(/^(\+\d{1,4})[-\s]?(\d{5,})$/);
                        if (match) {
                            countryCode = match[1]; // +91
                            phoneNumber = match[2]; // 9876543210
                        }
                    } else {
                        // Assume no country code, just the number
                        phoneNumber = fullContact;
                    }

                    $('#user_id').val(response.data.id);
                    $('input[name="old_email"]').val(response.data.user_email);
                    $('input[name="user_email"]').val(response.data.user_email);
                    $('input[name="user_name"]').val(response.data.user_name);
                    $('input[name="first_name"]').val(response.data.d_f_name);
                    $('input[name="last_name"]').val(response.data.d_l_name);
                    $('select[name="user_active"]').val(response.data.user_active);
                    $('select[name="user_role"]').val(response.data.user_role);
                    $('#user_password').val(""); // Clear password field
                    $('input[name="select_service"]').val(response.data.select_service);
                    $('#user_password').val(response.data.user_password).removeAttr('required');
                    $('#confirm_password').val(response.data.user_password).removeAttr('required');
                    $('#addUserModal').modal('show');
                    $('input[name="country_code"]').val(response.data.d_country_code);
                    $('input[name="phone_number"]').val(phoneNumber);

                    // Trigger live check after autofill
                    $('#password-warning').hide();
                } else {
                    showErrorMessage("Error fetching details.")
                }
            }, "json");
        });

        $(document).on('click', '.add-btn', function() {
            $('#userForm')[0].reset(); // Clear form fields
            $('#user_id').val(''); // Clear hidden user_id field if present
            $('#addUserModal').modal('show'); // Open modal
        });

        // Clear Form When Edit Modal is Closed
        $('#editUserModal, #addUserModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Reset form fields
            $('#user_id').val(''); // Clear user ID field
            $('#password-warning').hide(); 
        });

        $('#userForm').submit(function(event) {
            event.preventDefault();

            // Get form values
            let email = $('input[name="user_email"]').val().trim();
            // let contact = $('input[name="user_contact"]').val().trim();
            let password = $('#user_password').val();
            let confirmPassword = $('#confirm_password').val();
            let countryCode = $('input[name="country_code"]').val().trim();
            let phoneNumber = $('input[name="phone_number"]').val().trim();

            // Email validation (basic regex)
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showErrorMessage("Please enter a valid email address.");
                return false;
            }

            // Country code validation (e.g., +91)
            if (!/^\+\d{1,4}$/.test(countryCode)) {
                showErrorMessage("Invalid country code (e.g., +91).");
                return false;
            }

            // Contact validation (only numbers, exactly 10 digits)
            let contactRegex = /^[0-9]{1,14}$/;
            if (!contactRegex.test(phoneNumber)) {
                showErrorMessage("Contact number must be between 1 to 14 digits and contain only numbers.");
                return false;
            }

            // Password confirmation validation
            if (password !== confirmPassword) {
                showErrorMessage("Passwords do not match.");

                return false;
            }

            $.post('create-user.php', $(this).serialize(), function(response) {
                if (response.status === "success") {
                    $('#addUserModal, #editUserModal').modal('hide');
                    showSuccessMessage(response.message);

                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    showErrorMessage(response.message);
                }
            }, "json");
        });

        // Clear form fields on modal close
        $('#addUserModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        $(document).on('click', '.delete-btn', function() {
            if (confirm("Are you sure?")) {
                $.post('delete-user.php', {
                    id: $(this).data('id')
                }, function(response) {
                    if (response.status === "success") {
                        showSuccessMessage("User deleted successfully!"); // Show success message
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    } else {
                        showErrorMessage(response.message);
                    }

                }, "json");
            }
        });

        function showSuccessMessage(message) {
            $('#successMessage').text(message);
            $('#successAlert').fadeIn().delay(3000).fadeOut();
        }

        function showErrorMessage(message) {
            $('#errorMessage').text(message);
            $('#errorAlert').fadeIn().delay(3000).fadeOut();
        }

        $('#confirm_password').on('input', function () {
            let password = $('#user_password').val();
            let confirmPassword = $(this).val();

            if (password !== confirmPassword) {
                $('#password-warning').show();
            } else {
                $('#password-warning').hide();
            }
        });

        function toggleAccountDropdown() {
            const dropdown = document.getElementById('accountDropdownMenu');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown on click outside
        document.addEventListener('click', function (event) {
            const btn = document.getElementById('accountDropdownBtn');
            const menu = document.getElementById('accountDropdownMenu');
            if (!btn.contains(event.target) && !menu.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
        
    </script>

</body>

</html>