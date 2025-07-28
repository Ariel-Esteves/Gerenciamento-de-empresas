<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Endereco;

class EmpresasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for testing pagination
        $empresasData = [
            [
                'cnpj' => '11.222.333/0001-81',
                'razao_social' => 'Tech Solutions Ltda',
                'nome_fantasia' => 'TechSol',
                'email' => 'contato@techsol.com',
                'telefone' => '(11) 99999-1111',
                'ramo_atividade' => 'Tecnologia',
                'endereco' => [
                    'cep' => '01310-100',
                    'logradouro' => 'Av. Paulista',
                    'numero' => '1000',
                    'complemento' => 'Sala 101',
                    'bairro' => 'Bela Vista',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP'
                ]
            ],
            [
                'cnpj' => '22.333.444/0001-92',
                'razao_social' => 'Construtora ABC S.A.',
                'nome_fantasia' => 'ABC Construções',
                'email' => 'info@abcconstrucoes.com',
                'telefone' => '(21) 88888-2222',
                'ramo_atividade' => 'Construção Civil',
                'endereco' => [
                    'cep' => '22071-900',
                    'logradouro' => 'Av. Atlântica',
                    'numero' => '2000',
                    'complemento' => null,
                    'bairro' => 'Copacabana',
                    'cidade' => 'Rio de Janeiro',
                    'estado' => 'RJ'
                ]
            ],
            [
                'cnpj' => '33.444.555/0001-03',
                'razao_social' => 'Alimentícia Bom Sabor Ltda',
                'nome_fantasia' => 'Bom Sabor',
                'email' => 'vendas@bomsabor.com',
                'telefone' => '(31) 77777-3333',
                'ramo_atividade' => 'Alimentício',
                'endereco' => [
                    'cep' => '30112-000',
                    'logradouro' => 'Rua da Bahia',
                    'numero' => '500',
                    'complemento' => 'Loja A',
                    'bairro' => 'Centro',
                    'cidade' => 'Belo Horizonte',
                    'estado' => 'MG'
                ]
            ],
            [
                'cnpj' => '44.555.666/0001-14',
                'razao_social' => 'Transportadora Rápida Express S.A.',
                'nome_fantasia' => 'Rápida Express',
                'email' => 'logistica@rapidaexpress.com',
                'telefone' => '(41) 66666-4444',
                'ramo_atividade' => 'Logística',
                'endereco' => [
                    'cep' => '80010-000',
                    'logradouro' => 'Rua XV de Novembro',
                    'numero' => '1500',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Curitiba',
                    'estado' => 'PR'
                ]
            ],
            [
                'cnpj' => '55.666.777/0001-25',
                'razao_social' => 'Consultoria Estratégica Inovação Ltda',
                'nome_fantasia' => 'Inovação Consulting',
                'email' => 'consultoria@inovacao.com',
                'telefone' => '(51) 55555-5555',
                'ramo_atividade' => 'Consultoria',
                'endereco' => [
                    'cep' => '90010-150',
                    'logradouro' => 'Rua dos Andradas',
                    'numero' => '800',
                    'complemento' => 'Conjunto 802',
                    'bairro' => 'Centro Histórico',
                    'cidade' => 'Porto Alegre',
                    'estado' => 'RS'
                ]
            ],
            [
                'cnpj' => '66.777.888/0001-36',
                'razao_social' => 'Indústria Metalúrgica do Sul Ltda',
                'nome_fantasia' => 'Metal Sul',
                'email' => 'producao@metalsul.com',
                'telefone' => '(47) 44444-6666',
                'ramo_atividade' => 'Metalúrgica',
                'endereco' => [
                    'cep' => '89010-500',
                    'logradouro' => 'Rua Blumenau',
                    'numero' => '300',
                    'complemento' => null,
                    'bairro' => 'Industrial',
                    'cidade' => 'Blumenau',
                    'estado' => 'SC'
                ]
            ],
            [
                'cnpj' => '77.888.999/0001-47',
                'razao_social' => 'Farmácia e Drogaria Saúde Total S.A.',
                'nome_fantasia' => 'Saúde Total',
                'email' => 'atendimento@saudetotal.com',
                'telefone' => '(62) 33333-7777',
                'ramo_atividade' => 'Farmacêutico',
                'endereco' => [
                    'cep' => '74000-020',
                    'logradouro' => 'Av. Goiás',
                    'numero' => '1200',
                    'complemento' => 'Loja 15',
                    'bairro' => 'Centro',
                    'cidade' => 'Goiânia',
                    'estado' => 'GO'
                ]
            ],
            [
                'cnpj' => '88.999.000/0001-58',
                'razao_social' => 'Agência de Marketing Digital Criativo Ltda',
                'nome_fantasia' => 'Criativo Digital',
                'email' => 'projetos@criativodigital.com',
                'telefone' => '(85) 22222-8888',
                'ramo_atividade' => 'Marketing Digital',
                'endereco' => [
                    'cep' => '60160-230',
                    'logradouro' => 'Av. Dom Luís',
                    'numero' => '900',
                    'complemento' => 'Sala 405',
                    'bairro' => 'Aldeota',
                    'cidade' => 'Fortaleza',
                    'estado' => 'CE'
                ]
            ],
            [
                'cnpj' => '99.000.111/0001-69',
                'razao_social' => 'Escola Técnica Profissionalizante Futuro S.A.',
                'nome_fantasia' => 'Escola Futuro',
                'email' => 'secretaria@escolafuturo.edu',
                'telefone' => '(81) 11111-9999',
                'ramo_atividade' => 'Educação',
                'endereco' => [
                    'cep' => '50010-000',
                    'logradouro' => 'Rua do Recife',
                    'numero' => '400',
                    'complemento' => null,
                    'bairro' => 'Boa Viagem',
                    'cidade' => 'Recife',
                    'estado' => 'PE'
                ]
            ],
            [
                'cnpj' => '10.111.222/0001-70',
                'razao_social' => 'Loja de Roupas Fashion Style Ltda',
                'nome_fantasia' => 'Fashion Style',
                'email' => 'vendas@fashionstyle.com',
                'telefone' => '(71) 00000-1010',
                'ramo_atividade' => 'Moda e Vestuário',
                'endereco' => [
                    'cep' => '40070-110',
                    'logradouro' => 'Rua Chile',
                    'numero' => '100',
                    'complemento' => 'Térreo',
                    'bairro' => 'Pelourinho',
                    'cidade' => 'Salvador',
                    'estado' => 'BA'
                ]
            ],
            [
                'cnpj' => '12.345.678/0001-90',
                'razao_social' => 'Oficina Mecânica Automotiva Peças e Serviços Ltda',
                'nome_fantasia' => 'Auto Peças',
                'email' => 'oficina@autopecas.com',
                'telefone' => '(61) 98765-4321',
                'ramo_atividade' => 'Automotivo',
                'endereco' => [
                    'cep' => '70040-010',
                    'logradouro' => 'SCS Quadra 2',
                    'numero' => '50',
                    'complemento' => 'Bloco A',
                    'bairro' => 'Asa Sul',
                    'cidade' => 'Brasília',
                    'estado' => 'DF'
                ]
            ],
            [
                'cnpj' => '23.456.789/0001-01',
                'razao_social' => 'Petshop Amigos dos Animais S.A.',
                'nome_fantasia' => 'Amigos Pet',
                'email' => 'cuidados@amigospet.com',
                'telefone' => '(67) 87654-3210',
                'ramo_atividade' => 'Pet Shop',
                'endereco' => [
                    'cep' => '79002-070',
                    'logradouro' => 'Av. Afonso Pena',
                    'numero' => '2500',
                    'complemento' => null,
                    'bairro' => 'Centro',
                    'cidade' => 'Campo Grande',
                    'estado' => 'MS'
                ]
            ]
        ];

        foreach ($empresasData as $data) {
            // Create endereco first
            $endereco = Endereco::create($data['endereco']);
            
            // Create empresa with endereco_id
            Empresa::create([
                'cnpj' => $data['cnpj'],
                'razao_social' => $data['razao_social'],
                'nome_fantasia' => $data['nome_fantasia'],
                'email' => $data['email'],
                'telefone' => $data['telefone'],
                'ramo_atividade' => $data['ramo_atividade'],
                'endereco_id' => $endereco->id,
            ]);
        }
    }
}
