dojo.require( "modulo.padrao.geral" );

var objGeral = new modulo.padrao.geral();

window.onbeforeunload = function()
{
   objGeral.loading( true );
};

dojo.addOnLoad( function()
{
    objGeral.fimCarregando();
});

 function loginSistema( )
 {
    objGeral.loading( true );

    if( document.getElementById('remember').checked )
        var remb = 1;
    else
        var remb = 0;
    
    var dataPost = {
        usuario:  document.getElementById('usuario').value,
        senha:    document.getElementById('senha').value,
        remember: remb
    };

    if( objGeral.empty( dataPost.usuario ) || objGeral.empty( dataPost.senha  ) ){
        
        objGeral.loading(false);

        $('#form-auth').find('.msg').remove();
        $('#form-auth').prepend('<div class="msg"><div class="error">'+objGeral.translate('Informe usuário e senha')+'</div></div>');
        
        var loginWindow = $('.cont .wrapper .block.login');

        var left = loginWindow.position().left>parseInt(loginWindow.css('margin-left'))
                        ? loginWindow.position().left
                        : loginWindow.css('margin-left');
                        loginWindow
                        .css('margin-left',left)
                        .effect('shake', null, 100);
        mbe.init.smallMessage();
        
    }else{
        
        var obj = {
            url: baseUrl + "/auth/login",
            handle: 'json',
            data: dataPost,
            callback: function( json )
            {
                $('#form-auth').find('.msg').remove();
                if( json.valid ){
                    
                    var msgText = objGeral.translate('Operação realizada com sucesso.');
                    $('#form-auth').prepend('<div class="msg"><div class="info">' + msgText + '</div></div>');
                    window.location.href=baseUrl+"/index";
                }else{

                    var msgText = ( json.message[0].message ? json.message[0].message : objGeral.translate('Erro ao executar operação') );
                    $('#form-auth').prepend('<div class="msg"><div class="error">'+ msgText +'</div></div>');
                    var loginWindow = $('.cont .wrapper .block.login');

                    var left = loginWindow.position().left>parseInt(loginWindow.css('margin-left'))
                                    ? loginWindow.position().left
                                    : loginWindow.css('margin-left');
                                    loginWindow
                                    .css('margin-left',left)
                                    .effect('shake', null, 100);
                    mbe.init.smallMessage();
                }
                objGeral.loading(false);
            },
            error: objGeral.translate('Erro ao efetuar login!')
        }

        objGeral.buscaAjax( obj );
    }
    
    return false;
 }

 function recoverySistema( )
 {
    objGeral.loading( true );

    var dataPost = {
        email:  document.getElementById('email').value
    };

    if( objGeral.empty( dataPost.email ) ){
        
        objGeral.loading(false);

        $('#form-recovery').find('.msg').remove();
        $('#form-recovery').prepend('<div class="msg"><div class="error">'+objGeral.translate('Informe e-mail')+'</div></div>');
        
        var loginWindow = $('.cont .wrapper .block.login');

        var left = loginWindow.position().left>parseInt(loginWindow.css('margin-left'))
                        ? loginWindow.position().left
                        : loginWindow.css('margin-left');
                        loginWindow
                        .css('margin-left',left)
                        .effect('shake', null, 100);
        mbe.init.smallMessage();
        
    }else{
        
        var obj = {
            url: baseUrl + "/auth/recovery",
            handle: 'json',
            data: dataPost,
            callback: function( json )
            {
                $('#form-recovery').find('.msg').remove();
                if( json.valid ){
                    
                    var msgText =  objGeral.translate('Operação realizada com sucesso.');
                    $('#form-recovery').prepend('<div class="msg"><div class="info">' + msgText + '</div></div>');
                    window.location.href=baseUrl+"/index";
                }else{

                    var msgText = objGeral.translate('Erro ao executar operação') ;
                    $('#form-recovery').prepend('<div class="msg"><div class="error">' + msgText + '</div></div>');
                    var loginWindow = $('.cont .wrapper .block.login');

                    var left = loginWindow.position().left>parseInt(loginWindow.css('margin-left'))
                                    ? loginWindow.position().left
                                    : loginWindow.css('margin-left');
                                    loginWindow
                                    .css('margin-left',left)
                                    .effect('shake', null, 100);
                    mbe.init.smallMessage();
                }
                objGeral.loading(false);
            },
            error: objGeral.translate('Erro ao recuperar senha!')
        }

        objGeral.buscaAjax( obj );
    }
    return false;
 }

mbe = {
	showActions:false,
	showActionObject:false,
	showActionsTimeOut:0,
	animationsSpeed:'fast',
	pbInterval:0,
        
	login: {
		ready: function() {

			//wrapper css
			$('.cont.fixed .wrapper').css({
				'width': 'auto',
				'text-align': 'center'
			});
                        
			//init login tabs
			$('.cont .wrapper .block.login .top .gray ul li a').click(function(){
				$('.cont .wrapper .block.login .top .gray ul li a').removeClass('active');
				$(this).addClass('active');
				$('.login-tabs').removeClass('active');
				$('#'+$(this).attr('href')).addClass('active');
				return false;
			});

		}
	},
	init: {
		smallMessage: function() {
			//init small message close
			$('.cont .wrapper .block .content .msg div span.close').remove();
			$('.cont .wrapper .block .content .msg div').append('<span class="close" title="Close">&nbsp;</span>');

			$('.cont .wrapper .block .content .msg div span.close ').unbind('click');
			$('.cont .wrapper .block .content .msg div span.close ').click(function(){
				$(this).parent().fadeTo(mbe.animationsSpeed, 0).slideUp(mbe.animationsSpeed);
			});

			$('.cont .wrapper .block .content .msg ul').append('<li class="close" title="Close">&nbsp;</li>');
			$('.cont .wrapper .block .content .msg ul li.close').click(function(){
				$(this).parent().fadeTo(mbe.animationsSpeed, 0).slideUp(mbe.animationsSpeed);
			});
		},
		bigMessage: function() {
			//init big message close
			$('.cont .wrapper .message span.close').remove();
			$('.cont .wrapper .message').append('<span class="close" title="Close">&nbsp</span>');

			$('.cont .wrapper .message span.close').unbind('click');
			$('.cont .wrapper .message span.close').click(function(){
				$(this).parent().fadeTo(mbe.animationsSpeed, 0).slideUp(mbe.animationsSpeed);
			});
		}
	},
	ready:function(){
            
		//init the big messages
		mbe.init.bigMessage();

		//init the small messages
		mbe.init.smallMessage();
	}
}

$(document).ready(mbe.ready);