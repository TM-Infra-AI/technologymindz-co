<title>Customer Login</title>

<head>
	<link rel="fav-icon" href="images/logo.png" type="image/png">
	<style>
		/* My Account Dropdown */
		#my_account {
			position: relative;
			cursor: pointer;
		}

		#account_dropdown {
			position: absolute;
			right: 0;
			top: 100%;
			background: #015fb1;
			width: 180px;
			display: none;
			box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
			border-radius: 5px;
			overflow: hidden;
			z-index: 10;
		}

		#account_dropdown a {
			display: block;
			padding: 10px;
			text-decoration: none;
			color: #fff;
			transition: background 0.3s;
		}

		#account_dropdown a:hover {
			color: black;
		}

		/* Show dropdown on hover */
		#my_account:hover #account_dropdown {
			display: block;
		}

		#header {
			overflow: visible !important;
		}
		@media screen and (max-width: 700px) {
		    #header .mobile_home{
		        display: none !important;
		    }
		}
	</style>
</head>
<?php
if (!isset($_SESSION['user_email'])) {
	header('Location:login.php');
	exit;
}
?>
<?php
require('../../includes/config.php');

$stmt = $connect->prepare("SELECT * FROM digi_card WHERE user_email = ? ORDER BY id DESC LIMIT 30");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$stmt_query = $stmt->get_result();
$cardrow = $stmt_query->fetch_assoc();

$_SESSION['card_id_inprocess'] = $cardrow['id'];
?>


<header id="header">
	<div class="logo" onclick="location.href='index.php'">
		<img src="images/logo.png"> <!--<h3>Customer Login</h3>-->
	</div>
	<div class="mobile_home">&equiv;</div>
	<div class="head_txt">
		<h3><?php
			if (isset($_SESSION['first_name'])) {
				echo 'Hi ' . $_SESSION['first_name'] . '!';
			} else {
				echo 'Hi Guest!';
			}
			?>
		</h3>

		<!-- <h3>
        <a href="my_account.php"><i class="fa fa-lock"></i> Change Password</a>
		
		</h3>
		<h3> <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></h3> -->
		<!-- My Account Dropdown -->
		<div id="my_account">
			<h3 id="account_btn">
				<i class="fa fa-user"></i> My Account <i class="fa fa-caret-down"></i>
			</h3>
			<div id="account_dropdown">
				<a href="select_theme.php">
					<i class="fa fa-edit"></i> Edit Profile
				</a>
				<?php  if($cardrow['user_role'] == 'admin') { ?>
				<a href="<?php echo BASE_URL . '/admin/users.php'; ?>">
					<i class="fa fa-cogs"></i> Admin Portal
				</a>
				<?php } ?>
				<a href="leads.php"><i class="fa fa-users"></i> Leads</a>
				<a href="my_account.php"><i class="fa fa-lock"></i> Change Password</a>
				<a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
			</div>
		</div>
	</div>


</header>

<!-- JavaScript for Click to Open -->
<script>
	$(document).ready(function() {
		$("#account_btn").on("click", function(event) {
			event.stopPropagation(); // Prevent closing when clicking inside
			$("#account_dropdown").toggle();
		});

		// Close dropdown when clicking outside
		$(document).on("click", function(event) {
			if (!$("#my_account").is(event.target) && $("#my_account").has(event.target).length === 0) {
				$("#account_dropdown").hide();
			}
		});

		// Mobile Menu Toggle
		$("#mobile_menu").on("click", function() {
			$("#header").toggleClass("add_height");
		});
	});
</script>


<script>
	$(document).ready(function() {
		$('.mobile_home').on('click', function() {
			$('#header').toggleClass('add_height');

		})
	})
</script>
