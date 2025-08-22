function openAuth(pin){

	const authDiv = document.createElement("div");
	authDiv.id = "transactionAuthDiv";
	authDiv.className = "container-box mobile-width-80 system-width-50";
	authDiv.innerHTML = '<center>\
	<span class="color-9 mobile-font-size-13 system-font-size-15">TRANSACTION CODE</span><br>\
	<span class="color-9 mobile-font-size-12 system-font-size-14">Enter Details in the FORM below</span><br>\
	<input type="number" id="trans-code" value="'+pin+'" hidden/>\
	<input autofocus type="number" id="auth-code" class="input-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="****"/>\
	<input onclick=checkAuth(); style="display:none;" type="button" style="height: auto;" class="button-box color-8 bg-2 mobile-font-size-12 system-font-size-14 mobile-width-25 system-width-22" value="Authenticate"/>\
	</center>';
	
	document.body.appendChild(authDiv);
}

	setInterval(function(){
		if(document.getElementById("auth-code").value.trim().length == 4){
			checkAuth();
		}
	},500);
	
	function checkAuth(){
		const correctTransCode = document.getElementById("trans-code").value.trim();
		const inputtedTransCode = document.getElementById("auth-code").value.trim();
		if(inputtedTransCode !== ""){
			if(inputtedTransCode == correctTransCode){
				authResponse("200");
			}else{
				authResponse("201");
			}
			document.getElementById("transactionAuthDiv").remove();
		}else{
			alertPopUp("Enter Transaction PIN! ");
		}
	}

function alertPopUp(information){
	
	const alertDiv = document.createElement("div");
	alertDiv.id = "alert-div";
	alertDiv.className = "container-box mobile-width-80 system-width-50";
	alertDiv.innerHTML = '<center><div style="display: block;" class="color-9 mobile-font-size-12 system-font-size-14 mobile-width-100 system-width-100">'+information+'</div><br><button onclick=document.getElementById("alert-div").remove(); id="alert-ok" style="height: auto;" class="button-box color-8 bg-2 mobile-font-size-12 system-font-size-14 mobile-width-15 system-width-12">Ok</button></center>';
	
	document.body.appendChild(alertDiv);
}

function nenterkey_function(evt)
{
	var bool=true;
	if(evt.keyCode==13){
		bool=false;
	}
	return bool;
}