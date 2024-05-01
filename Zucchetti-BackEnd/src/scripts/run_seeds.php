<?php
// Define o caminho base para os scripts
$basePath = "G:\\TesteZucchetti\\Zucchetti-BackEnd\\src\\scripts\\";

// Lista de scripts para executar
$scripts = [
    "create_admin_user.php",
    "create_customer.php",
    "create_paymentmethod.php",
    "create_product.php",
    "create_sale.php"
];

// Executa cada script
foreach ($scripts as $script) {
    echo "Executando $script...\n";
    include $basePath . $script;
    echo "$script concluído.\n";
    // Adiciona um atraso de 5 segundos entre cada script, se necessário
    sleep(5);
}

echo "Todos os scripts foram executados.\n";
?>
