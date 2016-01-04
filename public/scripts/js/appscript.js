/**
 * Modulo Default
 */
dojo.require( "modulo.padrao.geral" );
dojo.require( "modulo.padrao.grid" );
dojo.require( "modulo.padrao.preferencia" );
dojo.require( "modulo.padrao.lembrete" );
dojo.require( "modulo.padrao.lembreteconfig" );

/**
 * Modulo Master
 */
dojo.require( "modulo.master.modulo" );
dojo.require( "modulo.master.tela" );
dojo.require( "modulo.master.configuracao" );
dojo.require( "modulo.master.acao" );
dojo.require( "modulo.master.menu" );
dojo.require( "modulo.master.auditoria" );
dojo.require( "modulo.master.linguagem" );
dojo.require( "modulo.master.linguagemtermo" );

/**
 * Modulo Admin
 */
dojo.require( "modulo.admin.perfil" );
dojo.require( "modulo.admin.cargo" );
dojo.require( "modulo.admin.filial" );
dojo.require( "modulo.admin.terceiro" );
dojo.require( "modulo.admin.usuario" );
dojo.require( "modulo.admin.funcionario" );
dojo.require( "modulo.admin.permissao" );
dojo.require( "modulo.admin.traducao" );

/**
 * Modulo Gestão
 */
dojo.require( "modulo.gestao.projeto" );
dojo.require( "modulo.gestao.atividade" );
dojo.require( "modulo.gestao.timeline" );

/**
 *  Modulo Almoxarifado
 */
dojo.require( "modulo.almoxarifado.situacaoativo" );
dojo.require( "modulo.almoxarifado.tipoativo" );
dojo.require( "modulo.almoxarifado.ativo" );
dojo.require( "modulo.almoxarifado.estoque" );
dojo.require( "modulo.almoxarifado.unidademedida" );
dojo.require( "modulo.almoxarifado.tipoproduto" );
dojo.require( "modulo.almoxarifado.produto" );

/**
 *  Modulo Financeiro
 */
dojo.require( "modulo.financeiro.banco" );
dojo.require( "modulo.financeiro.budget" );
dojo.require( "modulo.financeiro.cartaocredito" );
dojo.require( "modulo.financeiro.categoria" );
dojo.require( "modulo.financeiro.cheque" );
dojo.require( "modulo.financeiro.conta" );
dojo.require( "modulo.financeiro.documentofiscal" );
dojo.require( "modulo.financeiro.duplicata" );
dojo.require( "modulo.financeiro.lancamento" );
dojo.require( "modulo.financeiro.lancamentocartao" );
dojo.require( "modulo.financeiro.tipolancamento" );
dojo.require( "modulo.financeiro.recibo" );
dojo.require( "modulo.financeiro.reconciliacao" );


/**
 * Modulo Relatorios
 */
dojo.require( "modulo.relatorio.ativo" );
dojo.require( "modulo.relatorio.balancete" );
dojo.require( "modulo.relatorio.boleto" );
dojo.require( "modulo.relatorio.cheque" );
dojo.require( "modulo.relatorio.conta" );
dojo.require( "modulo.relatorio.contaprojeto" );
dojo.require( "modulo.relatorio.duplicata" );
dojo.require( "modulo.relatorio.estoque" );
dojo.require( "modulo.relatorio.faturacartao" );
dojo.require( "modulo.relatorio.lancamentocartao" );
dojo.require( "modulo.relatorio.projeto" );
dojo.require( "modulo.relatorio.timeline" );
dojo.require( "modulo.relatorio.transferencia" );
dojo.require( "modulo.relatorio.reconciliacaobancaria" );
dojo.require( "modulo.relatorio.reconciliacaocartaocredito" );



/**
 * Modulo Custom
 */
dojo.require( "modulo.custom.TreeCheck" );
dojo.require( "modulo.custom.TreeMenu" );
dojo.require( "modulo.custom.ForestStoreModel" );

/**
 * Elementos Dojo
 */
//dojo.require( "dijit.Tree" );

var dhUltMMosuse     = new Date();
function verificaSessao()
{   
    if( objGeral.empty( dojo.cookie( 'auth_' + objAppid ) ) ){
        
        var  dhnow  = new Date();
        var  difMin = dojo.date.difference( dhUltMMosuse, dhnow, 'minute' );
        
        if(  difMin >= 20 ){

            dhUltMMosuse = new Date();
            objGeral.msgAlerta( objGeral.translate('Sua sessão foi expirada.') );
            setTimeout("location=( baseUrl + '/auth/logout/' );",100);  

        }else{

                dhUltMMosuse = new Date();
        }
    }
}

dojo.addOnLoad(

    function()
    {
        objGrid.gridHeight = parseInt( screen.height ) / 2.2 ;
        document.getElementById('tab-home').style.height= ( parseInt( screen.height ) / 1.7 ) +"px";
        objGeral.fimCarregando();
        window.setInterval( defaultLembrete.buscaLembretes, 30 * 1000 );   
        window.addEventListener('keydown', function () { verificaSessao(); });
        window.addEventListener('onmouseover', function () { verificaSessao(); });
    }
);