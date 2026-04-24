<?php
$funcionarios = [
    [
        'id' => 1,
        'nome' => 'Ana Pereira',
        'cargo' => 'Analista de RH',
        'setor' => 'Recursos Humanos',
        'email' => 'ana.pereira@empresa.com',
        'status' => 'Ativo',
    ],
    [
        'id' => 2,
        'nome' => 'Carlos Souza',
        'cargo' => 'Desenvolvedor Full Stack',
        'setor' => 'Tecnologia',
        'email' => 'carlos.souza@empresa.com',
        'status' => 'Ativo',
    ],
    [
        'id' => 3,
        'nome' => 'Juliana Lima',
        'cargo' => 'Assistente Administrativo',
        'setor' => 'Administrativo',
        'email' => 'juliana.lima@empresa.com',
        'status' => 'Férias',
    ],
    [
        'id' => 4,
        'nome' => 'Marcos Silva',
        'cargo' => 'Supervisor de Produção',
        'setor' => 'Operações',
        'email' => 'marcos.silva@empresa.com',
        'status' => 'Ativo',
    ],
];
?>

<section class="page-header">
    <h2>Funcionários</h2>
    <button type="button" class="btn-primary">+ Novo Funcionário</button>
</section>

<section class="table-card">
    <div class="table-card__top">
        <p>Lista de colaboradores cadastrados</p>
        <input type="search" placeholder="Buscar funcionário..." aria-label="Buscar funcionário">
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Setor</th>
                    <th>E-mail</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?= htmlspecialchars($funcionario['id']) ?></td>
                        <td><?= htmlspecialchars($funcionario['nome']) ?></td>
                        <td><?= htmlspecialchars($funcionario['cargo']) ?></td>
                        <td><?= htmlspecialchars($funcionario['setor']) ?></td>
                        <td><?= htmlspecialchars($funcionario['email']) ?></td>
                        <td>
                            <span class="status <?= $funcionario['status'] === 'Ativo' ? 'ativo' : 'ferias' ?>">
                                <?= htmlspecialchars($funcionario['status']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button type="button" class="btn-link">Ver</button>
                            <button type="button" class="btn-link">Editar</button>
                            <button type="button" class="btn-link danger">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
