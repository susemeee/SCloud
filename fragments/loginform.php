<div id="background"></div>
<!-- login form below -->
<div id='login-wrap' class="login-wrapper">
	<!-- don't be confused with alert component of bootstrap -->
	<div id="login" class="alertbox">
		<div id="noscript">
			<!-- this block will be overlayed when the browser of client does not support JavaScript. -->
		</div>
		<form id="login-form" action="_core/noscript.php" method="POST">
			<div id="login-title" class="alertbox-header black">
				<h3>SCloud login</h3>
			</div>
			<div class="alertbox-body">
				<input type="text" placeholder="아이디 입력" name="id" required maxlength="40"><br>
				<input type="password" placeholder="비밀번호 입력" name="pw" required maxlength="40">
				<input type="hidden" name="action" value="auth">
			</div>
			<input type="button" id="btn-login" class="alertbox-btn alertbox-btn-left" value="로그인">
			<input type="button" id="btn-register" class="alertbox-btn alertbox-btn-right" value="회원가입">
		</form>
	</div>
	<div id="login-failure" class="alert alert-danger hidden">
	</div>
</div>

<script src="./_js/login.js"></script>
</div>