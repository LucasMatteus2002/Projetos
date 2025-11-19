// Programa para cadastro de alunos em uma escola de Idiomas, utilizando login para o administrador acessar o Menu e ser capaz de realizar o cadastro, atualização de dados e exclusão de alunos no sistema.

// Desenvolvedores: Lucas Matteus, Raissa Krause

// Data de conclusão do código: 16/11/2025

/* OBSERVAÇÕES SOBRE A ESTRUTURA DO CÓDIGO: todas as funções foram definidas fora da função principal, para melhor organização. As funções que foram utilizadas foram:
SalvarDados(contém passagem de parâmetro para que funcione em conjunto com a função CadastrarAluno) LerDados, CadastrarAluno, AtualizarDados e ExcluirAluno.
as funções aparecem antes, e a função principal chama cada uma delas na parte final do código. */

// Inclusão das bibliotecas que serão utilizadas no programa.
#include <stdio.h> 
#include <string.h>
#include <stdlib.h>
#include <locale.h>

// Função para salvar os dados dos alunos no arquivo, incluindo as credenciais de login, no caso ele irá salvar o nome, registro, período, email, e senha.
// Essa função (Salvar Dados) tem passagem de parâmetro, ou seja, as variáveis que estão dentro do parêntese vão receber os valores que serão atribuídos na função Registrar Aluno.
void SalvarDados(char nome[], int registro, char período[], char email[], char senha[]) {
    FILE *arquivo = fopen("dados_escola.txt", "a");
    if (arquivo == NULL) {
        printf("Erro ao abrir o arquivo.\n");
        exit(1);
    }

    // Trecho de código responsável por escrever os dados do aluno no arquivo, foram escritos assim para que os dados fiquem separados por linhas.
    fprintf(arquivo, "===DADOS DE CADASTRO DOS ALUNOS DA FISK DA VIDA===\n");
    fprintf(arquivo, "Registro: %d\n", registro);
    fprintf(arquivo, "Nome: %s", nome);
    fprintf(arquivo, "Periodo: %s", período);
    fprintf(arquivo, "Email: %s", email);
    fprintf(arquivo, "Senha: %s", senha);
    fprintf(arquivo, "------------------------------\n");
    fclose(arquivo);
}

// Função para realizar a leitura dos dados que foram salvos no arquivo, lê linha por linha.
void LerDados() {
    FILE *arquivo = fopen("dados_escola.txt", "r");
    char linha[300]; 
    
    // Condicional para verificar se o arquivo foi de fato aberto na função Salvar Dados, se não tiver sido aberto, uma mensagem de erro é exibida.
    if (arquivo == NULL) {
        printf("Erro ao abrir o arquivo.\n");
        exit(1);
    }

    printf("Dados Cadastrados na Escola de Idiomas\n");
    while (fgets(linha, sizeof(linha), arquivo )){
        printf("%s", linha);
    }
    fclose(arquivo);
}

// Função que realiza o cadastro do aluno, recebe os valores das variáveis nome, registro, periodo, email e senha.
void CadastrarAluno() {
    char nome[30];
    int registro;
    char periodo[10];
    char email[40];
    char senha[100];

    printf ("Digite o nome do aluno: ");
    fgets (nome, sizeof(nome), stdin);

    printf ("Digite o registro do aluno: ");
    scanf ("%d" , &registro);
    getchar();

    printf ("Digite o periodo em que o aluno estuda: ");
    fgets (periodo, sizeof(periodo), stdin);

    printf ("Digite o email do aluno:");
    fgets (email, sizeof(email), stdin);

    printf ("Defina uma senha de acesso: ");
    fgets (senha, sizeof (senha), stdin);

    printf("Aluno cadastrado com sucesso");

    // Aqui, os valores que foram atribuídos em cada uma das variáveis são passados para a função Salvar Dados.
    SalvarDados(nome, registro, periodo, email, senha); 

}

// Função para atualizar os dados do aluno, onde o administrador poderá alterar os dados que foram salvos anteriormente no arquivo.
void AtualizarDados() {

    // Declaração das variáveis que serão utilizadas na função.
    int registroAlvo;
    char nome[30];
    char periodo[10];
    char email[40];
    char senha[200];
    char linha[300];
    int registroAtual;

    // Abertura dos arquivos, um para leitura e outro temporário para escrita dos dados atualizados.
    FILE *arquivo = fopen("dados_escola.txt", "r");
    FILE *arquivoTemp = fopen("dados_temp.txt", "w");
    
    // Condicional para verificar se os arquivos foram abertos corretamente.
    if (arquivo == NULL || arquivoTemp == NULL) {
        printf("Erro ao abrir o arquivo.\n");
        exit(1);
    }
    printf("Digite o registro do aluno que deseja atualizar: ");
    scanf("%d", &registroAlvo);
    getchar();
    int encontrado = 0;
    
    // Loop para ler o arquivo linha por linha e procurar pelo registro do aluno a ser atualizado.
    while (fgets(linha, sizeof(linha), arquivo)) {
        
        // Verifica se a linha atual contém o registro do aluno.
        if (sscanf(linha, "Registro: %d", &registroAtual) == 1 && registroAtual == registroAlvo) {
            encontrado = 1;

            printf("Digite o novo nome do aluno: ");
            fgets(nome, sizeof(nome), stdin);

            printf("Digite o novo periodo do aluno: ");
            fgets(periodo, sizeof(periodo), stdin);

            printf("Digite o novo email do aluno: ");
            fgets(email, sizeof(email), stdin);

            printf("Digite a nova senha do aluno: ");
            fgets(senha, sizeof(senha), stdin);

            fprintf(arquivoTemp, "===DADOS DE CADASTRO DOS ALUNOS DA FISK DA VIDA===\n");
            
            // Escreve os novos dados no arquivo temporário.
            fprintf(arquivoTemp, "Nome: %s", nome);
            fprintf(arquivoTemp, "Registro: %d\n", registroAlvo);
            fprintf(arquivoTemp, "Periodo: %s", periodo);
            fprintf(arquivoTemp, "Email: %s", email);
            fprintf(arquivoTemp, "Senha: %s", senha);
            fprintf(arquivoTemp, "------------------------------\n");
            // Pula as próximas 5 linhas do arquivo original, que correspondem aos dados antigos do aluno.
            for (int i = 0; i < 5; i++) {
                fgets(linha, sizeof(linha), arquivo);
            }
        } else {
            fputs(linha, arquivoTemp);
        }

    }
    // Fecha os arquivos após a leitura e escrita.
    fclose(arquivo);
    fclose(arquivoTemp);

    // Verifica se o aluno foi encontrado e atualiza o arquivo original.
    if (encontrado) {
        remove("dados_escola.txt");
        rename("dados_temp.txt", "dados_escola.txt");
        printf("Dados atualizados com sucesso.\n");
    } else {
        // Se o aluno não for encontrado, remove o arquivo temporário e exibe uma mensagem de erro.
        remove("dados_temp.txt");
        printf("Aluno com registro %d nao encontrado.\n", registroAlvo);
    }

}

void ExcluirAluno() {
    // Declaração das variáveis que serão utilizadas na função.
    int registroAlvo;
    char linha[300];
    int registroAtual;

    // Abertura dos arquivos, um para leitura e outro temporário para escrita dos dados atualizados.
    FILE *arquivo = fopen("dados_escola.txt", "r");
    FILE *arquivoTemp = fopen("dados_temp.txt", "w");
    
    // Condicional para verificar se os arquivos foram abertos corretamente.
    if (arquivo == NULL || arquivoTemp == NULL) {
        printf("Erro ao abrir o arquivo.\n");
        exit(1);
    }
    printf("Digite o registro do aluno que deseja excluir: ");
    scanf("%d", &registroAlvo);
    getchar();
    int encontrado = 0;
    
    // Loop para ler o arquivo linha por linha e procurar pelo registro do aluno a ser atualizado.
    while (fgets(linha, sizeof(linha), arquivo)) {
        
        // Verifica se a linha atual contém o registro do aluno.
        if (sscanf(linha, "Registro: %d", &registroAtual) == 1 && registroAtual == registroAlvo) {
            encontrado = 1;

            // Pula as próximas 5 linhas do arquivo original, que correspondem aos dados antigos do aluno.
            for (int i = 0; i < 5; i++) {
                fgets(linha, sizeof(linha), arquivo);
            }
        } else {
            fputs(linha, arquivoTemp);
        }

    }
    // Fecha os arquivos (principal e secundário) após realizar a leitura e escrita.
    fclose(arquivo);
    fclose(arquivoTemp);

    // Verifica se o aluno foi encontrado e atualiza o arquivo original.
    if (encontrado) {
        remove("dados_escola.txt");
        rename("dados_temp.txt", "dados_escola.txt");
        printf("Aluno excluido com sucesso.\n");
    } else {
        // Se o aluno não for encontrado, remove o arquivo temporário e exibe uma mensagem.
        remove("dados_temp.txt");
        printf("Aluno com registro %d nao encontrado.\n", registroAlvo);
    }
    
}

// Abertura da função principal, onde serão chamadas as funções que foram definidas na parte de cima do código. 
int main(){
     // Função para apresentar a acentuação da forma correta na escrita em português. 
    setlocale(LC_ALL, "Portuguese");

    char usuarioAdmin[] = "SuperLucas";
    char senhaAdmin[] = "RaissaArtista";

    char usuario[20];
    char senha[20];

    printf(" LOGIN DO ADMINISTRADOR \n");

    printf("Usuario: ");
    fgets(usuario, sizeof(usuario), stdin);
    usuario[strcspn(usuario, "\n")] = '\0';

    printf("Senha: ");
    fgets(senha, sizeof(senha), stdin);
    senha[strcspn(senha, "\n")] = '\0';

    if (strcmp(usuario, usuarioAdmin) != 0 || strcmp(senha, senhaAdmin) != 0) {
        printf("Usuario ou senha incorretos.\n");
        return 1;
    }

    printf("Login realizado com sucesso.\n");

    // Int opcao é necessário pra controlar o menu.
    int opcao;

    // Início do loop do-while, basicamente utilizado para a implementação do MENU CRUD.
    do {
        printf ("\nMENU DO ADMINISTRADOR\n");
        printf ("1. Cadastrar novo aluno\n");
        printf ("2. Ler Dados do aluno\n");
        printf ("3. Atualizar dados do aluno\n");
        printf ("4. Excluir Aluno do sistema\n");
        printf ("5. Sair\n");
        printf ("Escolha uma opcao: ");
        scanf ("%d", &opcao);
        getchar (); // Limpa o buffer do teclado
        
        switch (opcao){
            case 1:
            printf("Cadastrando um novo aluno\n");
            CadastrarAluno();
            break;

            case 2:
            LerDados();
            break;

            case 3:
            AtualizarDados(); 
            break;

            case 4:
            ExcluirAluno();    
            break;
       
            case 5:
            printf ("Encerrando...\n");
            break;
        }



    } while (opcao != 5);
    
    

    return 0;
}
