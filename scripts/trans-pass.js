
function authResponse(code){
	if(code == 200){
		document.getElementById("proceed").style.display = "none";
		document.getElementById("buyPRODUCT").click();
	}else if(code == 201){
		alertPopUp("Incorrect Transaction Code");
	}
}
