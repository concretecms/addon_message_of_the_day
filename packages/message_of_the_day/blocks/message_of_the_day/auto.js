// JavaScript Document

var message_of_the_dayBlock ={
	
	serviceURL: $("input[name=message_of_the_dayServices]").val() + '',
	
	init:function(){ 
		var animate=$('#ccm-message_of_the_dayAnimateCheckbox');
		animate.change(function(){ message_of_the_dayBlock.showAnimationOpts(this) })
		animate.click(function(){ message_of_the_dayBlock.showAnimationOpts(this) })
		
		$('#ccm-block-fields input[name="using_stacks"]').change(function(){ message_of_the_dayBlock.showPageSelector(); });
		$('#ccm-block-fields input[name="using_stacks"]').click(function(){ message_of_the_dayBlock.showPageSelector(); });
	},
	
	showPageSelector:function(){/* this can just be switching the thing because you're not selecting areas for a stack */ 
		var checked=$('#using_stacksOn').get(0).checked;
		var scrapbooking=$('#using_scrapbookOn').get(0).checked;
		if(checked){
			 $('#ccm-motd-page-selector').css('display','none'); 
			 $('#scrapbook-list').hide();
			 $('#stack-list').show();
			/* this.refreshAreas($('#bookPool_fromScrapbookOnRadio').val());*/
		}else if(scrapbooking){
			$('#stack-list').hide();
			$('#ccm-motd-page-selector').css('display','none'); 
			$('#scrapbook-list').show();
		}else{
			$('#ccm-motd-page-selector').css('display','block');
			$('#stack-list').hide();
			$('#scrapbook-list').hide();
		}
	},
	 
	showAnimationOpts:function(cb){
		if(cb.checked){
			$('#ccm-message_of_the_dayAnimationOptions').css('display','block');
		}else{
			$('#ccm-message_of_the_dayAnimationOptions').css('display','none');
		}		
	},
	
	refreshAreas:function(cID){  
		$.ajax({ 
			url: this.serviceURL+'?mode=refreshAreas&cID='+parseInt(cID)+'&selectedArea=' + encodeURIComponent($('#blockPool_arHandle').val()),
			success: function(response){
				//$('#miniSurveyPreviewWrap').html(msg);  
				$('#blockPool_arHandle').html(response);
			}
		});
	},

	validate:function(){
		var failed=0; 
		
		/*
		var titleF=$('#ccm_search_block_title');
		var titleV=titleF.val();
		if(!titleV || titleV.length==0 ){
			alert(ccm_t('search-title'));
			titleF.focus();
			failed=1;
		}
		*/
        var durationVal=$("input[name='duration']").val();
        if (durationVal>24) {
            alert( ccm_t('max-duration-hours') ); 
			failed=1;
        }
		var cID=$("input[name='blockPool_cID']").val(); 
		if((!parseInt(cID)) && $("input[name=bookPool_fromScrapbook]:checked").val() == 0) {
			alert( ccm_t('target-page-required') ); 
			failed=1;
		}
		
		if(failed){
			ccm_isBlockError=1;
			return false;
		}
		return true;
	} 
}
$(function(){ message_of_the_dayBlock.init(); });

ccm_message_of_the_daySelectSitemapNode = function(cID, cName) { 
	var par = $(ccmActivePageField).parent().find('.ccm-summary-selected-page-label');
	var pari = $(ccmActivePageField).parent().find('[name=blockPool_cID]');
	par.html(cName);
	pari.val(cID);	
	message_of_the_dayBlock.refreshAreas(cID);
}
ccmValidateBlockForm = function() { return message_of_the_dayBlock.validate(); }