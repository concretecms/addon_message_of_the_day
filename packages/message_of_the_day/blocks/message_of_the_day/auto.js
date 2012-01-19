// JavaScript Document

var randomizerBlock ={
	
	serviceURL: $("input[name=randomizerServices]").val() + '',
	
	init:function(){ 
		var animate=$('#ccm-randomizerAnimateCheckbox');
		animate.change(function(){ randomizerBlock.showAnimationOpts(this) })
		animate.click(function(){ randomizerBlock.showAnimationOpts(this) })
		 
		$('#ccm-block-fields input[name="blockSource"]').change(function(){ randomizerBlock.showPageSelector(); });
		$('#ccm-block-fields input[name="blockSource"]').click(function(){ randomizerBlock.showPageSelector(); });
	},
	
	showPageSelector:function() { /* this can just be switching the thing because you're not selecting areas for a stack */ 
		var blockSource = $("input[@name=blockSource]:checked").val();
		// hide all 
		$('.ccm-blockSource-option').hide();
		
		switch(blockSource) {
		case 'stack':
			$('#ccm-randomizer-stack-list').show();
			break;
		case 'page':
			$('#ccm-randomizer-page-selector').show();
			break;
		case 'scrapbook':
			$('#ccm-randomizer-scrapbook-list').show();
			break;
		}
		
	},
	 
	showAnimationOpts:function(cb){
		if(cb.checked){
			$('#ccm-randomizerAnimationOptions').css('display','block');
		}else{
			$('#ccm-randomizerAnimationOptions').css('display','none');
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
	}

}

$(function(){ randomizerBlock.init(); });

ccm_randomizerSelectSitemapNode = function(cID, cName) { 
	var par = $(ccmActivePageField).parent().find('.ccm-summary-selected-item-label');
	var pari = $(ccmActivePageField).parent().find('[name=blockPool_cID]');
	par.html(cName);
	pari.val(cID);	
	randomizerBlock.refreshAreas(cID);
}