<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for all three tables
        $empresasData = [
            [
                'tipo_documento' => 'CNPJ',
                'cnpj' => '12.345.678/0001-90',
                'razao_social' => 'Tech Solutions Ltda',
                'nome_fantasia' => 'TechSol',
                'email' => 'contato@techsol.com.br',
                'telefone' => '(11) 99999-9999',
                'ramo_atividade' => 'Tecnologia',
                'endereco' => [
                    'cep' => '01234-567',
                    'logradouro' => 'Avenida Paulista',
                    'numero' => '1000',
                    'complemento' => 'Sala 101',
                    'bairro' => 'Bela Vista',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'contrato_techsol.pdf', 'tipo_anexo' => 'contrato'],
                    ['nome_arquivo' => 'logo_techsol.png', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'tipo_documento' => 'CNPJ',
                'cnpj' => '98.765.432/0001-10',
                'razao_social' => 'Construção Brasil S.A.',
                'nome_fantasia' => 'CB Construções',
                'email' => 'info@cbconstrucoes.com.br',
                'telefone' => '(21) 88888-8888',
                'ramo_atividade' => 'Construção Civil',
                'endereco' => [
                    'cep' => '22000-000',
                    'logradouro' => 'Rua das Laranjeiras',
                    'numero' => '500',
                    'complemento' => null,
                    'bairro' => 'Laranjeiras',
                    'cidade' => 'Rio de Janeiro',
                    'estado' => 'RJ'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'alvara_cb.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'projeto_obra1.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'tipo_documento' => 'CNPJ',
                'cnpj' => '11.222.333/0001-44',
                'razao_social' => 'Alimentos Frescos Ltda',
                'nome_fantasia' => 'Fresh Foods',
                'email' => 'vendas@freshfoods.com.br',
                'telefone' => '(85) 77777-7777',
                'ramo_atividade' => 'Alimentício',
                'endereco' => [
                    'cep' => '60000-000',
                    'logradouro' => 'Rua do Comércio',
                    'numero' => '123',
                    'complemento' => 'Galpão A',
                    'bairro' => 'Centro',
                    'cidade' => 'Fortaleza',
                    'estado' => 'CE'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'certificado_anvisa.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'cardapio.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'foto_estabelecimento.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'tipo_documento' => 'CNPJ',
                'cnpj' => '22.333.444/0001-55',
                'razao_social' => 'Transportadora Rápida Express S.A.',
                'nome_fantasia' => 'Rápida Express',
                'email' => 'logistica@rapidaexpress.com.br',
                'telefone' => '(41) 66666-6666',
                'ramo_atividade' => 'Logística',
                'endereco' => [
                    'cep' => '80000-000',
                    'logradouro' => 'Rua XV de Novembro',
                    'numero' => '1500',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Curitiba',
                    'estado' => 'PR'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'licenca_transporte.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'frota_veiculos.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'tipo_documento' => 'CNPJ',
                'cnpj' => '33.444.555/0001-66',
                'razao_social' => 'Consultoria Estratégica Inovação Ltda',
                'nome_fantasia' => 'Inovação Consulting',
                'email' => 'consultoria@inovacao.com.br',
                'telefone' => '(51) 55555-5555',
                'ramo_atividade' => 'Consultoria',
                'endereco' => [
                    'cep' => '90000-000',
                    'logradouro' => 'Rua dos Andradas',
                    'numero' => '800',
                    'complemento' => 'Conjunto 802',
                    'bairro' => 'Centro Histórico',
                    'cidade' => 'Porto Alegre',
                    'estado' => 'RS'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'portfolio_projetos.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'certificacoes.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '44.555.666/0001-77',
                'razao_social' => 'Indústria Metalúrgica do Sul Ltda',
                'nome_fantasia' => 'Metal Sul',
                'email' => 'producao@metalsul.com.br',
                'telefone' => '(47) 44444-4444',
                'ramo_atividade' => 'Metalúrgica',
                'endereco' => [
                    'cep' => '89000-000',
                    'logradouro' => 'Rua Industrial',
                    'numero' => '300',
                    'complemento' => null,
                    'bairro' => 'Distrito Industrial',
                    'cidade' => 'Blumenau',
                    'estado' => 'SC'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'licenca_ambiental.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'catalogo_produtos.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'fabrica_foto.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '55.666.777/0001-88',
                'razao_social' => 'Farmácia e Drogaria Saúde Total S.A.',
                'nome_fantasia' => 'Saúde Total',
                'email' => 'atendimento@saudetotal.com.br',
                'telefone' => '(62) 33333-3333',
                'ramo_atividade' => 'Farmacêutico',
                'endereco' => [
                    'cep' => '74000-000',
                    'logradouro' => 'Avenida Goiás',
                    'numero' => '1200',
                    'complemento' => 'Loja 15',
                    'bairro' => 'Centro',
                    'cidade' => 'Goiânia',
                    'estado' => 'GO'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'licenca_farmacia.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'responsavel_tecnico.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '66.777.888/0001-99',
                'razao_social' => 'Agência de Marketing Digital Criativo Ltda',
                'nome_fantasia' => 'Criativo Digital',
                'email' => 'projetos@criativodigital.com.br',
                'telefone' => '(85) 22222-2222',
                'ramo_atividade' => 'Marketing Digital',
                'endereco' => [
                    'cep' => '60100-000',
                    'logradouro' => 'Avenida Dom Luís',
                    'numero' => '900',
                    'complemento' => 'Sala 405',
                    'bairro' => 'Aldeota',
                    'cidade' => 'Fortaleza',
                    'estado' => 'CE'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'cases_sucesso.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'equipe_criativa.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '77.888.999/0001-00',
                'razao_social' => 'Escola Técnica Profissionalizante Futuro S.A.',
                'nome_fantasia' => 'Escola Futuro',
                'email' => 'secretaria@escolafuturo.edu.br',
                'telefone' => '(81) 11111-1111',
                'ramo_atividade' => 'Educação',
                'endereco' => [
                    'cep' => '50000-000',
                    'logradouro' => 'Rua da Educação',
                    'numero' => '400',
                    'complemento' => null,
                    'bairro' => 'Boa Viagem',
                    'cidade' => 'Recife',
                    'estado' => 'PE'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'credenciamento_mec.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'grade_curricular.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'campus_foto.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '88.999.000/0001-11',
                'razao_social' => 'Loja de Roupas Fashion Style Ltda',
                'nome_fantasia' => 'Fashion Style',
                'email' => 'vendas@fashionstyle.com.br',
                'telefone' => '(71) 99999-0000',
                'ramo_atividade' => 'Moda e Vestuário',
                'endereco' => [
                    'cep' => '40000-000',
                    'logradouro' => 'Rua Chile',
                    'numero' => '100',
                    'complemento' => 'Térreo',
                    'bairro' => 'Pelourinho',
                    'cidade' => 'Salvador',
                    'estado' => 'BA'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'colecao_verao.jpg', 'tipo_anexo' => 'imagem'],
                    ['nome_arquivo' => 'catalogo_roupas.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '10.111.222/0001-33',
                'razao_social' => 'Oficina Mecânica Automotiva Peças e Serviços Ltda',
                'nome_fantasia' => 'Auto Peças Express',
                'email' => 'oficina@autopecasexpress.com.br',
                'telefone' => '(61) 88888-1111',
                'ramo_atividade' => 'Automotivo',
                'endereco' => [
                    'cep' => '70000-000',
                    'logradouro' => 'SCS Quadra 2',
                    'numero' => '50',
                    'complemento' => 'Bloco A',
                    'bairro' => 'Asa Sul',
                    'cidade' => 'Brasília',
                    'estado' => 'DF'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'certificacao_oficina.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'equipamentos.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '20.222.333/0001-44',
                'razao_social' => 'Petshop Amigos dos Animais S.A.',
                'nome_fantasia' => 'Amigos Pet',
                'email' => 'cuidados@amigospet.com.br',
                'telefone' => '(67) 77777-2222',
                'ramo_atividade' => 'Pet Shop',
                'endereco' => [
                    'cep' => '79000-000',
                    'logradouro' => 'Avenida Afonso Pena',
                    'numero' => '2500',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Campo Grande',
                    'estado' => 'MS'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'veterinario_responsavel.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'produtos_pets.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '30.333.444/0001-55',
                'razao_social' => 'Clínica Médica Bem Estar e Saúde Ltda',
                'nome_fantasia' => 'Clínica Bem Estar',
                'email' => 'agendamento@clinicabemestar.com.br',
                'telefone' => '(31) 66666-3333',
                'ramo_atividade' => 'Saúde',
                'endereco' => [
                    'cep' => '30000-000',
                    'logradouro' => 'Rua da Bahia',
                    'numero' => '1500',
                    'complemento' => '3º Andar',
                    'bairro' => 'Centro',
                    'cidade' => 'Belo Horizonte',
                    'estado' => 'MG'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'licenca_sanitaria.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'equipe_medica.jpg', 'tipo_anexo' => 'imagem'],
                    ['nome_arquivo' => 'convenios.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '40.444.555/0001-66',
                'razao_social' => 'Padaria e Confeitaria Pão Dourado Ltda',
                'nome_fantasia' => 'Pão Dourado',
                'email' => 'vendas@paodourado.com.br',
                'telefone' => '(19) 55555-4444',
                'ramo_atividade' => 'Alimentício',
                'endereco' => [
                    'cep' => '13000-000',
                    'logradouro' => 'Rua Barão de Jaguara',
                    'numero' => '800',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Campinas',
                    'estado' => 'SP'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'alvara_funcionamento.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'cardapio_paes.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '50.555.666/0001-77',
                'razao_social' => 'Academia de Ginástica Corpo Saudável S.A.',
                'nome_fantasia' => 'Academia Corpo Saudável',
                'email' => 'matriculas@corposaudavel.com.br',
                'telefone' => '(27) 44444-5555',
                'ramo_atividade' => 'Fitness',
                'endereco' => [
                    'cep' => '29000-000',
                    'logradouro' => 'Avenida Nossa Senhora da Penha',
                    'numero' => '1000',
                    'complemento' => 'Térreo',
                    'bairro' => 'Praia do Canto',
                    'cidade' => 'Vitória',
                    'estado' => 'ES'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'equipamentos_academia.jpg', 'tipo_anexo' => 'imagem'],
                    ['nome_arquivo' => 'planos_mensalidade.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'cnpj' => '60.666.777/0001-88',
                'razao_social' => 'Empresa de Limpeza e Conservação Total Clean Ltda',
                'nome_fantasia' => 'Total Clean',
                'email' => 'contratos@totalclean.com.br',
                'telefone' => '(48) 33333-6666',
                'ramo_atividade' => 'Limpeza',
                'endereco' => [
                    'cep' => '88000-000',
                    'logradouro' => 'Rua Felipe Schmidt',
                    'numero' => '200',
                    'complemento' => 'Sala 301',
                    'bairro' => 'Centro',
                    'cidade' => 'Florianópolis',
                    'estado' => 'SC'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'contratos_servicos.pdf', 'tipo_anexo' => 'contrato'],
                    ['nome_arquivo' => 'equipamentos_limpeza.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '70.777.888/0001-99',
                'razao_social' => 'Gráfica Impressão Perfeita e Design Ltda',
                'nome_fantasia' => 'Impressão Perfeita',
                'email' => 'orcamentos@impressaoperfeita.com.br',
                'telefone' => '(32) 22222-7777',
                'ramo_atividade' => 'Gráfica',
                'endereco' => [
                    'cep' => '36000-000',
                    'logradouro' => 'Rua Halfeld',
                    'numero' => '600',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Juiz de Fora',
                    'estado' => 'MG'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'portfolio_grafica.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'maquinas_impressao.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '80.888.999/0001-00',
                'razao_social' => 'Floricultura Jardim das Flores e Plantas Ltda',
                'nome_fantasia' => 'Jardim das Flores',
                'email' => 'vendas@jardimdasflores.com.br',
                'telefone' => '(43) 11111-8888',
                'ramo_atividade' => 'Floricultura',
                'endereco' => [
                    'cep' => '86000-000',
                    'logradouro' => 'Avenida Higienópolis',
                    'numero' => '1200',
                    'complemento' => 'Loja B',
                    'bairro' => 'Centro',
                    'cidade' => 'Londrina',
                    'estado' => 'PR'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'catalogo_flores.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'arranjos_florais.jpg', 'tipo_anexo' => 'imagem']
                ]
            ],
            [
                'cnpj' => '90.999.000/0001-11',
                'razao_social' => 'Distribuidora de Bebidas Gelada Boa S.A.',
                'nome_fantasia' => 'Bebidas Gelada Boa',
                'email' => 'vendas@geladaboa.com.br',
                'telefone' => '(34) 99999-9000',
                'ramo_atividade' => 'Distribuição',
                'endereco' => [
                    'cep' => '38000-000',
                    'logradouro' => 'Avenida João Naves de Ávila',
                    'numero' => '2000',
                    'complemento' => 'Galpão 5',
                    'bairro' => 'Santa Mônica',
                    'cidade' => 'Uberlândia',
                    'estado' => 'MG'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'licenca_distribuicao.pdf', 'tipo_anexo' => 'documento'],
                    ['nome_arquivo' => 'estoque_bebidas.jpg', 'tipo_anexo' => 'imagem'],
                    ['nome_arquivo' => 'fornecedores.pdf', 'tipo_anexo' => 'documento']
                ]
            ],
            [
                'tipo_documento' => 'CPF',
                'cnpj' => '123.456.789-01',
                'razao_social' => 'João Silva ME',
                'nome_fantasia' => 'João Silva Prestador',
                'email' => 'joao.silva@email.com',
                'telefone' => '(11) 91234-5678',
                'ramo_atividade' => 'Consultoria Individual',
                'endereco' => [
                    'cep' => '01310-000',
                    'logradouro' => 'Avenida Paulista',
                    'numero' => '1578',
                    'complemento' => 'Sala 1201',
                    'bairro' => 'Bela Vista',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP'
                ],
                'anexos' => [
                    ['nome_arquivo' => 'cpf_joao.pdf', 'tipo_anexo' => 'documento']
                ]
            ]
        ];

        // Insert data with relationships
        foreach ($empresasData as $empresaData) {
            // 1. Insert Endereco first
            $enderecoId = DB::table('enderecos')->insertGetId([
                'cep' => $empresaData['endereco']['cep'],
                'logradouro' => $empresaData['endereco']['logradouro'],
                'numero' => $empresaData['endereco']['numero'],
                'complemento' => $empresaData['endereco']['complemento'],
                'bairro' => $empresaData['endereco']['bairro'],
                'cidade' => $empresaData['endereco']['cidade'],
                'estado' => $empresaData['endereco']['estado'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Insert Empresa with endereco_id
            $empresaId = DB::table('empresas')->insertGetId([
                'tipo_documento' => $empresaData['tipo_documento'] ?? 'CNPJ',
                'cnpj' => $empresaData['cnpj'],
                'razao_social' => $empresaData['razao_social'],
                'nome_fantasia' => $empresaData['nome_fantasia'],
                'email' => $empresaData['email'],
                'telefone' => $empresaData['telefone'],
                'ramo_atividade' => $empresaData['ramo_atividade'],
                'endereco_id' => $enderecoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Insert Anexos for this empresa
            foreach ($empresaData['anexos'] as $anexoData) {
                DB::table('anexos')->insert([
                    'nome_arquivo' => $anexoData['nome_arquivo'],
                    'caminho_arquivo' => '/uploads/' . $anexoData['nome_arquivo'],
                    'tipo_mime' => $this->getMimeType($anexoData['nome_arquivo']),
                    'tamanho' => rand(1024, 5000000), // Random file size
                    'tipo_anexo' => $anexoData['tipo_anexo'],
                    'empresa_id' => $empresaId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Dados inseridos: ' . count($empresasData) . ' empresas com endereços e anexos!');
    }

    /**
     * Get MIME type based on file extension
     */
    private function getMimeType($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        return match($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream'
        };
    }
}
