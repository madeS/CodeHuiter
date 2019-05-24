<?php
?>
<div class="admin_access">
	<style>
		.admin_access .v_inner { margin: 0 0 0 40px;}
		.admin_access .v_inner.styled_form textarea.in { min-width: 600px; max-width: 600px}
		.admin_access .v_inner.styled_form input[type=text].in { min-width: 600px; max-width: 600px}
		.admin_access .v_inner.styled_form input[readonly].in { background-color: #eee;}
		.admin_access h1 {margin-bottom: 10px;}
		.admin_access .version {padding: 5px 0;}
		.admin_access .version > div {display: inline-block; width: 200px; min-height: 10px;}
		.admin_access .version .title {font-weight: bold;}
	</style>
	<script>
		app.guisetcookie = function(el){
			return mjsa.mFormSubmit(el,'/auth/gui_setcookie_submit',{callback:function(resp,data){
			}});
		};
	</script>
	<h1 class="headtext">Additional GUI</h1> 
	<div class="v_inner">   
		<h3 class="headtext">Gui setcookie</h3>
		<div class="v_inner m_form styled_form">   
			<div class="row">
				<span>Name:</span><input type="text" class="in enter_submit" name="name" value="">
			</div>
			<div class="row">
				<span>Value:</span><input type="text" class="in enter_submit" name="value" value="">
			</div>
			<div class="row in_error"></div>
			<div class="row">
				<span>&nbsp;</span><input type="button" 
				data-action="guisetcookie" class="btn green m_form_submit action" value="Set" />
			</div>
			<div>
				<h3 class="headtext">Current cookie</h3>	
				<?php $this->mm->debugParam($_COOKIE); ?>
			</div>
			<div class="response">
					
			</div>
		</div>	

	</div>


	
</div>