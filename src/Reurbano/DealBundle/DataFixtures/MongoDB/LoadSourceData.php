<?php

namespace Reurbano\DealBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Reurbano\DealBundle\Document\Source;

class LoadSourceData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load($manager) {
        $citySP = $manager->getRepository('ReurbanoCoreBundle:City')->findBySlug('sao-paulo');
        $cityRJ = $manager->getRepository('ReurbanoCoreBundle:City')->findBySlug('rio-de-janeiro');
        $cityNacional = $manager->getRepository('ReurbanoCoreBundle:City')->findBySlug('oferta-nacional');
        $catServicos = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('servicos');
        $catProdutos = $manager->getRepository('ReurbanoDealBundle:Category')->findBySlug('produtos');
        $siteGroupon = $manager->getRepository('ReurbanoDealBundle:Site')->findOneById(1);
        $sitePxUrbano = $manager->getRepository('ReurbanoDealBundle:Site')->findOneById(4);
        
        $source = new Source();
        $source->setTitle("Helicóptero de controle remoto infravermelho, de R$ 132,00 por R$ 89,90, no ProdutosOFF, com frete grátis");
        $source->setFilename('teste/groupon.jpg');
        $source->setThumb('teste/groupon.jpg');
        $source->setUrl('http://www.groupon.com.br/ofertas/sao-paulo---premium/Produtos-off/663013');
        $source->setSite($siteGroupon);
        $source->setPrice(132);
        $source->setPriceOffer(89.9);
        $source->setCity($citySP);
        $source->setCategory($catProdutos);
        $source->setTotalcoupons(370);
        $source->setTotalsell(33263);
        $source->setRules('Limite de 1 groupon para você
    Você deve validar sua compra no site www.produtosoff.com.br. Acesse seu painel de usuário no ProdutosOff, insira os dados para envio e o código do seu Groupon
    Seu groupon só poderá ser utilizado 2 dias úteis após o seu recebimento
    O cadastramento correto no hotsite é imprescindível para que o produto seja entregue
    Entrega em até 50 dias
    Atendimento ao cliente pelo chat online 24 horas no site www.produtosoff.com.br ou pelo e-mail suporte@produtosoff.com.br');
        $source->setDetails('
				<h3 class="subHeadline">Helicóptero de controle remoto infravermelho, de R$ 132,00 por R$ 89,90, no ProdutosOFF, com frete grátis</h3>
				<p>Enquanto nos deparamos com os aviões que, estabilizados, voam pelos ares, e os pássaros que, com tamanha facilidade, rasgam o céu, nos perguntamos como um helicóptero consegue realizar tal façanha com maestria.<br><br>Parece um jogo de empurra, em que uma peça em movimento ora o faz girar para a esquerda, ora para a direita, ora o 
pende para baixo, ora o desestabiliza em alta velocidade... Mas, no fim das contas, ele voa.<br><br>O Groupon ainda não tem em seu rol de ofertas essa máquina para ofertar para você, mas descolou, em parceria com a ProdutosOFF, um produto que fará seu tempo livre ficar muito mais divertido e emocionante: helicóptero de controle remoto infravermelho, de R$ 132,00 por R$ 89,90!<br><br>Especialmente concebido para iniciantes e fácil de voar com uma operação simples, esse brinquedo conta com uma estrutura leve e excelente controle de giroscópios, tornando-o ideal para crianças a partir de 8 anos de idade.<br><br>Faça seu helicóptero voar longe e comande essa aventura adquirindo já o seu Groupon!<br><br><br>Especificações técnicas:<br><br>Contém 3 canais para voar <br>On/Off com indicador de alimentação,<br>Bateria embutida - 30-40 min <br>Controle remoto alimentado por 6 pilhas AA <br>Ideal para crianças acima dos 8 anos de idade <br>Comprimento do cabo: 62 centímetros <br>Tamanho do helicóptero aproximado:&nbsp; 154 X 40 X 92 milímetros (L x W x H) <br>Tamanho do controle remoto aproximado: 130 X 115 X 40 milímetros (L x W x H) <br>Cor: Amarelo <br>Peso: 228 g<br><br><br><img title="/79/04/1312904310479.jpg" src="https://static.groupon.com.br/79/04/1312904310479.jpg" alt="/79/04/1312904310479.jpg">
</p><p>&nbsp;</p>
<script src="http://www.migroupon.com/cl" type="text/javascript"></script><p></p>
				<div class="other-deals"><br><br></div><div class="wrapper"></div>
				<div class="merchantContact">
					<h3 class="subHeadline">ProdutosOFF</h3>
	                  </div>
			');
        $source->setBusinessUrl('http://www.produtosoff.com.br');
        $source->setBusinessName('ProdutosOFF');
        $source->setExpiresAt(new \DateTime('2011-10-10'));
        $manager->persist($source);
        $manager->flush();
        
        $source = new Source();
        $source->setTitle("Corpinho de Sereia! 84% OFF em 3 Sessões de Manthus + 3 Sessões de Massagem Modeladora na Bella Opção Estétika (de R$600 por R$99). Use até 2 cupons");
        $source->setFilename('teste/teste1.jpg');
        $source->setThumb('teste/teste1.jpg');
        $source->setUrl('http://www.peixeurbano.com.br/sao-paulo-grande-abc/ofertas/bella-opcao-abc4YWEZCS');
        $source->setSite($sitePxUrbano);
        $source->setPrice(600);
        $source->setPriceOffer(99);
        $source->setCity($citySP);
        $source->setCategory($catServicos);
        $source->setTotalcoupons(17);
        $source->setTotalsell(1683);
        $source->setRules('Validade: 16 de agosto, 2011 a 16 de fevereiro, 2012
    Limite de uso de 2 cupons por pessoa
    O cupom estará disponível na sua conta do Peixe Urbano em até 24 horas após o encerramento da oferta
    Informar o código do cupom no momento do agendamento com a Bella Opção Estétika, mediante disponibilidade
    Válido exclusivamente para mulheres
    É necessário levar toalha
    Os tratamentos deverão ser realizados juntos a cada visita
    Para melhores resultados, é recomendável o intervalo mínimo de 3 dias entre as sessões
    Consulte as contraindicações
    Confira as Regras Gerais que se aplicam a todas as ofertas');
        $source->setDetails('
                        <p style="text-align: justify; ">
	A premiada atriz Moreia Beltrão estava ansiosa. Em poucas semanas ia estrear uma nova peça de teatro. Dessa vez sua personagem seria a protagonista do espetáculo, uma Rainha do Mar linda, elegante e esbelta. Moreia já tinha o texto na ponta da língua e todas as cenas ensaiadas, mas ainda não estava satisfeita. Faltava ajustar o visual para conseguir brilhar novamente nos palcos marinhos. O Peixe Urbano, fiel espectador, ficou sabendo do desejo da amiga e resolveu ajudar a atriz a dar um&nbsp;<i>up</i>&nbsp;nas curvas:&nbsp;<b>84% de desconto em 3 Sessões de Manthus + 3 Sessões de Massagem Modeladora na Bella Opção Estétika (de R$600 por R$99).</b></p>
<p style="text-align: justify; ">
	<span style="font-size:12px;">O profissionalismo da equipe liderada pela esteticista&nbsp;<b>Kátia Camargo</b>&nbsp;é o principal atrativo da Bella Opção Estétika. Inaugurado há 3 anos, o local oferece aos seus clientes um ambiente agradável, com salas e espaços de convivência climatizados para você ter a sensação de bem-estar, seja nos dias mais frios ou mais quentes do ano. Cada detalhe da decoração foi pensado de forma a proporcionar relaxamento aos olhos e à mente. E, para que a comodidade das Sereias seja ainda maior, a clínica conta com vagas de estacionamento gratuitas.</span></p>
<p style="text-align: justify; ">
	<span style="font-size:12px;">A oferta de hoje é ideal para as Sereias que querem modelar as curvas! Os tratamentos têm início com a&nbsp;<b>Avaliação Corporal</b>, que ocorre na primeira sessão. Através dela os profissionais detectam quais as prioridades e as necessidades de cada Ser Marinho. Em seguida, serão aplicadas&nbsp;<b>3 Sessões de Manthus</b>, que utiliza um aparelho com 3 cabeçotes que associam as correntes elétricas ao ultrassom. Isso ajuda a quebrar as células de gordura do corpo e a combater celulite e a flacidez da pele. Depois, você receberá&nbsp;<b>3 Sessões de</b>&nbsp;<b>Massagem Modeladora</b>, técnica vigorosa feita com creme lipotílico, em que as mãos do profissional trabalham a modelagem das regiões da cintura, do abdômen, das pernas e dos braços.</span></p>
<p style="text-align: justify; ">
	<span style="font-size:12px;">Vai perder a chance de desfilar a boa forma pelos 7 mares? Garanta já o seu cupom!<b>&nbsp;<strong style="font-weight: bold; ">A Bella Opção Estétika recebe o Cardume de segunda a sexta, das 8h às 19h, e sábado, das 8h às 16h.</strong></b></span></p>

                    ');
        $source->setBusinessUrl('http://www.bellaopcaoestetika.com');
        $source->setBusinessName('Bella Opção Estétika ');
        $source->setBusinessAddress('Avenida Padre Anchieta - 303 - Bairro Jardim');
        $source->setExpiresAt(new \DateTime('2012-02-16'));
        $manager->persist($source);
        $manager->flush();
        
        $source = new Source();
        $source->setTitle("Sereias Perfeitas com 91% OFF em 10 Sessões de Heccus + 10 de Thermolipo + 10 de Massagem Modeladora no Studio 5 Centro de Estética (de R$1.900 por R$179). Use até 2 cupons. Parcele em até 12x*");
        $source->setFilename('teste/teste2.jpg');
        $source->setThumb('teste/teste2.jpg');
        $source->setUrl('http://www.peixeurbano.com.br/rio-de-janeiro/ofertas/studio-5-centro-de-hkbmjp');
        $source->setSite($sitePxUrbano);
        $source->setPrice(1900);
        $source->setPriceOffer(179);
        $source->setCity($cityRJ);
        $source->setCategory($catServicos);
        $source->setTotalcoupons(189);
        $source->setTotalsell(33831);
        $source->setRules('Validade: 16 de agosto, 2011 a 16 de fevereiro, 2012
    Limite de uso de 2 cupons por pessoa
    O cupom estará disponível na sua conta do Peixe Urbano em até 24 horas após o encerramento da oferta
    Informar o código do cupom no momento do agendamento com o Studio 5 Centro de Estética, pelos telefones (21) 3978-5118 ou (21) 7801-5358, mediante disponibilidade
    Válido exclusivamente para mulheres
    Para melhores resultados, é recomendável intervalo mínimo de 24 horas entre as visitas
    Os procedimentos deverão ser realizados juntos em cada visita
    Consulte a polí­tica de parcelamento
    Confira as Regras Gerais que se aplicam a todas as ofertas');
        $source->setDetails('
                        <p style="text-align: justify; ">
	O alvoroço estava formado! Todos os ingressos foram vendidos e já havia fila de espera para as sessões do dia seguinte à estreia do mais novo filme de Nicole Fishman. O que estava na boca de todos os Tubarões apaixonados e Sereias curiosas é que a atriz nunca esteve tão linda. A sessão confirmou o boato. A aparição da Beldade na telona do Cinema Oceânico aguçou ainda mais a curiosidade de todos.&nbsp;<em style="font-style: italic; ">Qual deve ser o segredo de beleza dela?</em>, perguntava a Tainha.&nbsp;<em style="font-style: italic; ">Como ela consegue?</em>, indaga a Merluza. Antes dos créditos finais, rapidamente apareceu o Peixe Urbano com uma oferta perfeita para todas as musas do Cardume desfilarem tão exuberantes como a Estrela-do-Mar, e sem precisar de efeitos especiais:&nbsp;<strong style="font-weight: bold; ">91% de desconto em 10 Sessões de Heccus + 10 de Thermolipo + 10 de Massagem Modeladora no Studio 5 Centro de Estética (de R$1.900 por R$179).</strong><br>
	<br>
	Há mais de 20 anos cuidando da beleza do Cardume carioca, o Studio 5 Centro de Estética tem como objetivo principal a satisfação de seus clientes. Comandado por&nbsp;<strong style="font-weight: bold; ">Rosana Pereira</strong>, profissional renomada no mercado de estética facial e corporal, o espaço conta com uma equipe de profissionais altamente capacitados e em constante atualização.&nbsp; Em ambiente climatizado e clean, o local possuiu recepção confortável, salas exclusivas para diferentes procedimentos, além de serviço de copa. Tudo para promover comodidade e momentos especiais a quem frequenta a casa. &nbsp;<br>
	<br>
	Com a oferta de hoje, as Sereias poderão realizar um verdadeiro ritual de beleza com&nbsp;<strong style="font-weight: bold; ">10 Sessões de Heccus + 10 de Thermolipo + 10 de Massagem Modeladora</strong>. Primeiro, você escolhe 1 área do corpo para receber todos os benefícios deste combo nota 10!&nbsp; O primeiro tratamento é o&nbsp;<strong style="font-weight: bold; ">Heccus</strong>, ultrassom de alta potência que ajuda a combater a flacidez e a tão temida celulite e também a fortalecer a musculatura e promover a produção de colágeno. A&nbsp;<strong style="font-weight: bold; ">Thermolipo</strong>&nbsp;é o procedimento que, através de uma manta térmica, utiliza o calor para auxiliar na redução das gordurinhas indesejáveis. Para completar este tratamento incrível, a&nbsp;<strong style="font-weight: bold; ">Massagem Modeladora</strong>, com seus movimentos intensos e rápidos, contribui para mais tonificação muscular e oxigenação dos tecidos. Em aproximadamente 1 hora de cuidados mais que especiais por visita, as musas do Cardume vão deslizar por aí ainda mais deslumbrantes.&nbsp;<br>
	<br>
	Desfile seu&nbsp;<em style="font-style: italic; ">corpitcho</em>&nbsp;com muito orgulho pelo oceano! Fisgue logo seu cupom.&nbsp;<strong style="font-weight: bold; ">O Studio 5 Centro de Estética recebe o Cardume de segunda a sexta, das 8h às 20h.&nbsp;</strong></p>

                    ');
        $source->setBusinessUrl('http://studio5estetica.com.br');
        $source->setBusinessName('Studio 5 Centro de Estética');
        $source->setBusinessAddress('R. da Quitanda, 67 Sl 302 - Centro');
        $source->setBusinessCep('20011030');
        $source->setExpiresAt(new \DateTime('2012-02-16'));
        $manager->persist($source);
        $manager->flush();
        
        $source = new Source();
        $source->setTitle("Processador de alimentos Change a Bowhl Hamilton Beach+ frete. De R$ 479,90 por R$ 199, na Polishop (Oferta Nacional)");
        $source->setFilename('teste/teste3.jpg');
        $source->setThumb('teste/teste3.jpg');
        $source->setUrl('http://www.groupon.com.br/ofertas/oferta-nacional/Polishop/667956');
        $source->setSite($siteGroupon);
        $source->setPrice(479.9);
        $source->setPriceOffer(199);
        $source->setCity($cityNacional);
        $source->setCategory($catProdutos);
        $source->setTotalcoupons(176);
        $source->setTotalsell(35024);
        $source->setRules('1 groupon por pessoa
    Vale por 1 mês
    Frete grátis, com entrega em até 12 dias úteis
    2 dias úteis após o recebimento do groupon, você deve validar a compra acessando o hotsite da promoção www.polishop.com.br/groupon e fornecendo os dados necessários
    Oferta não cumulativa com outras promoções
    Informação pelos telefones (11) 3444-0186, das 9h às 18h, ou pelo e-mail groupon@polishop.com.br
');
        $source->setDetails('
				<h3 class="subHeadline">Processador de
alimentos Change a Bowhl Hamilton Beach+ frete. De R$ 479,90 por R$ 199, na
Polishop</h3>
				<p><br>A teoria da relatividade veio para mostrar que até mesmo o paraíso pode depender do referencial. Com o Groupon de hoje, grandes cozinheiros e amantes dos bons pratos descobrem que o melhor dos mundos pode significar legumes fatiados com facilidade, nenhuma louça suja na pia e braços sem dores na hora de ralar: por R$ 199, a oferta vale 1 Processador Change a&nbsp; Bowhl Hamilton Beach + frete grátis, na Polishop.<br><br>Desenvolvido para revolucionar todos os conceitos domésticos, o Change a Bowhl oferece 300 w de potência e uma única lâmina reversível capaz de cortes precisos até mesmo nos casos dos alimentos mais duros, sendo possível utilizá-lo para fatiar salame, legumes ou até mesmo para ralar coco para sobremesa. Em cada tipo diferente de corte é possível alternar a posição da lâmina.&nbsp; Encontre a tomada mais próxima, aperte o botão e assita tudo de camarote.<br><br>Confira as especificações técnicas do produto:<br><br>Marca: Hamilton Beach<br>Modelo: 70800 <br>Voltagem: 110V ou 220V <br>Espessura do alimento fatiado no aparelho: 3 mm <br>Capacidade de cada pote: Até 6 xícaras ou 1.419 ml <br>Todas as partes removíveis podem ir à lava-louças <br>Possui Pressionador de Alimentos <br>Composição: <br>Lâmina: Aço Inox <br>Cor predominante: Branco <br>Garantia do Fabricante: 1 Ano <br>Potência: 300w <br>Trava de segurança: Sim <br><br><br>Itens inclusos:<br>1 Change a Bowhl <br>1 Manual de instruções em português <br><br><br><img title="/69/13/1313174731369.jpg" src="https://static.groupon.com.br/69/13/1313174731369.jpg" alt="/69/13/1313174731369.jpg"><br><br></p>
				<div class="wrapper"></div>
				<div class="merchantContact">
					<h3 class="subHeadline">Polishop</h3>
					-&nbsp;-, -&nbsp;-<br>
					 <a onclick="window.open(this.href); return false;" href="http://www.polishop.com.br">http://www.polishop.com.br</a><br>
	                  </div>
				
			');
        $source->setBusinessUrl('http://www.polishop.com.br');
        $source->setBusinessName('Polishop');
        $source->setExpiresAt(new \DateTime('2011-09-15'));
        $manager->persist($source);
        $manager->flush();
        
    }
    public function getOrder()
    {
        return 1;
    }
}