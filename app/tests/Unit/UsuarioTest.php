<?php

namespace Tests\Unit;

use App\Enums\Papel;
use App\Modules\Usuario\Dto\AtualizacaoDto;
use App\Modules\Usuario\Dto\CadastroDto;
use App\Modules\Usuario\Dto\ListagemDto;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Usuario\Repository\UsuarioRepository;
use App\Modules\Usuario\Service\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de listagem retorna array vazio inicialmente
     */
    public function test_listagem_dto_retorna_array_vazio_inicialmente(): void
    {
        $dto = new ListagemDto();
        $this->assertEquals([], $dto->asArray());
    }

    /**
     * Teste se o DTO de cadastro aceita dados do usuário
     */
    public function test_cadastro_dto_aceita_dados_usuario(): void
    {
        $dto = new CadastroDto(
            nome: 'Novo Usuário',
            email: 'novo@email.com',
            senha: 'password123',
            papel: 'mecanico',
            status: 'ativo'
        );

        $this->assertInstanceOf(CadastroDto::class, $dto);
        $this->assertEquals('Novo Usuário', $dto->nome);
        $this->assertEquals('novo@email.com', $dto->email);
        $this->assertEquals('password123', $dto->senha);
        $this->assertEquals('mecanico', $dto->papel);
        $this->assertEquals('ativo', $dto->status);
    }

    /**
     * Teste se o DTO de atualização pode ser instanciado
     */
    public function test_atualizacao_dto_pode_ser_instanciado(): void
    {
        $dto = new AtualizacaoDto([]);
        $this->assertInstanceOf(AtualizacaoDto::class, $dto);
    }

    // ==================== TESTES DO ENUM ====================
    /**
     * Teste se o Enum Papel tem os valores corretos
     */
    public function test_enum_papel_tem_valores_corretos(): void
    {
        $this->assertEquals('atendente', Papel::ATENDENTE->value);
        $this->assertEquals('mecanico', Papel::MECANICO->value);
        $this->assertEquals('comercial', Papel::COMERCIAL->value);
        $this->assertEquals('gestor_estoque', Papel::GESTOR_ESTOQUE->value);
    }


    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model Usuario pode ser instanciado
     */
    public function test_usuario_model_pode_ser_instanciado(): void
    {
        $usuario = new Usuario();
        $this->assertInstanceOf(Usuario::class, $usuario);
    }

        /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_usuario_model_tem_tabela_correta(): void
    {
        $usuario = new Usuario();
        $this->assertEquals('usuario', $usuario->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_usuario_model_tem_fillable_corretos(): void
    {
        $usuario = new Usuario();

        $expectedFillable = [
            'nome',
            'email',
            'senha',
            'status',
            'excluido',
            'data_exclusao',
            'data_cadastro',
            'data_atualizacao'
        ];

        $this->assertEquals($expectedFillable, $usuario->getFillable());
    }

    /**
     * Teste se o método getAuthPassword retorna o campo senha
     */
    public function test_usuario_model_get_auth_password_retorna_senha(): void
    {
        $usuario = new Usuario();
        $usuario->senha = 'minhaSenhaSecreta';

        $this->assertEquals('minhaSenhaSecreta', $usuario->getAuthPassword());
    }

    /**
     * Teste se o método role retorna a relação BelongsTo correta
     */
    public function test_usuario_model_role_retorna_belongs_to(): void
    {
        $usuario = new Usuario();
        $relation = $usuario->role();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('role_id', $relation->getForeignKeyName());
        $this->assertEquals(\Spatie\Permission\Models\Role::class, $relation->getRelated()::class);
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_usuario_repository_pode_ser_instanciado(): void
    {
        $repository = new UsuarioRepository();
        $this->assertInstanceOf(UsuarioRepository::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_usuario_repository_tem_model_correto(): void
    {
        $model = UsuarioRepository::model();
        $this->assertInstanceOf(Usuario::class, $model);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_usuario_service_pode_ser_instanciado(): void
    {
        $repository = $this->createMock(UsuarioRepository::class);
        $service = new Service($repository);
        $this->assertInstanceOf(Service::class, $service);
    }
}
