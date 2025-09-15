# CKO Framework - Documentação Flutter Mobile

## 📱 Visão Geral

O CKO Framework Mobile é uma aplicação Flutter que oferece acesso completo às funcionalidades financeiras e de AI através de uma interface nativa para iOS e Android.

## 🏗️ Arquitetura Flutter

```
┌─────────────────────────────────────────────────────────────────┐
│                        Flutter App                             │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │   Screens   │  │   Widgets   │  │  Services   │            │
│  │   (UI)      │  │ (Reusable)  │  │   (API)     │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │   Models    │  │   Utils     │  │   Themes    │            │
│  │  (Data)     │  │ (Helpers)   │  │  (Design)   │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
└─────────────────────────────────────────────────────────────────┘
```

## 📁 Estrutura do Projeto

```
mobile/
├── lib/
│   ├── main.dart                    # Ponto de entrada
│   ├── app.dart                     # Configuração da app
│   ├── screens/                     # Telas da aplicação
│   │   ├── auth/
│   │   │   ├── login_screen.dart
│   │   │   └── register_screen.dart
│   │   ├── dashboard/
│   │   │   └── dashboard_screen.dart
│   │   ├── finance/
│   │   │   ├── cashflow_screen.dart
│   │   │   ├── trades_screen.dart
│   │   │   └── holdings_screen.dart
│   │   ├── ai/
│   │   │   └── ai_chat_screen.dart
│   │   └── profile/
│   │       └── profile_screen.dart
│   ├── widgets/                     # Widgets reutilizáveis
│   │   ├── common/
│   │   │   ├── custom_app_bar.dart
│   │   │   ├── loading_widget.dart
│   │   │   └── error_widget.dart
│   │   ├── finance/
│   │   │   ├── financial_card.dart
│   │   │   ├── transaction_list.dart
│   │   │   └── chart_widget.dart
│   │   └── ai/
│   │       ├── chat_bubble.dart
│   │       └── ai_status_widget.dart
│   ├── services/                    # Serviços e APIs
│   │   ├── api_service.dart
│   │   ├── auth_service.dart
│   │   ├── finance_service.dart
│   │   └── ai_service.dart
│   ├── models/                      # Modelos de dados
│   │   ├── user.dart
│   │   ├── transaction.dart
│   │   ├── trade.dart
│   │   ├── holding.dart
│   │   └── ai_response.dart
│   ├── utils/                       # Utilitários
│   │   ├── constants.dart
│   │   ├── formatters.dart
│   │   ├── validators.dart
│   │   └── storage.dart
│   ├── themes/                      # Temas e estilos
│   │   ├── app_theme.dart
│   │   ├── colors.dart
│   │   └── text_styles.dart
│   └── providers/                   # Gerenciamento de estado
│       ├── auth_provider.dart
│       ├── finance_provider.dart
│       └── ai_provider.dart
├── android/                         # Configuração Android
├── ios/                            # Configuração iOS
├── web/                            # Configuração Web
├── test/                           # Testes
├── pubspec.yaml                    # Dependências
└── README.md
```

## 🛠️ Tecnologias Utilizadas

### Core Framework
- **Flutter** 3.0+ - Framework de desenvolvimento
- **Dart** 3.0+ - Linguagem de programação
- **Material Design** - Design system Android
- **Cupertino** - Design system iOS

### State Management
- **Provider** - Gerenciamento de estado
- **Riverpod** - Gerenciamento de estado avançado (opcional)

### HTTP & API
- **Dio** - Cliente HTTP
- **Retrofit** - Geração de clientes API
- **Json Annotation** - Serialização JSON

### UI & Styling
- **Material Design 3** - Componentes modernos
- **Cupertino** - Componentes iOS
- **Custom Widgets** - Componentes personalizados

### Storage & Cache
- **Shared Preferences** - Armazenamento local
- **Hive** - Banco de dados local
- **Cached Network Image** - Cache de imagens

## 🔧 Configuração

### pubspec.yaml

```yaml
name: cko_framework_mobile
description: CKO Framework Mobile App
version: 1.0.0+1

environment:
  sdk: '>=3.0.0 <4.0.0'
  flutter: ">=3.0.0"

dependencies:
  flutter:
    sdk: flutter
  
  # UI
  cupertino_icons: ^1.0.2
  material_design_icons_flutter: ^7.0.7296
  
  # State Management
  provider: ^6.0.5
  riverpod: ^2.4.0
  
  # HTTP & API
  dio: ^5.3.2
  retrofit: ^4.0.3
  json_annotation: ^4.8.1
  
  # Storage
  shared_preferences: ^2.2.0
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  
  # Utils
  intl: ^0.18.1
  cached_network_image: ^3.3.0
  url_launcher: ^6.2.1
  permission_handler: ^11.0.1
  
  # Charts
  fl_chart: ^0.65.0
  
  # AI & Chat
  flutter_chat_ui: ^1.6.15

dev_dependencies:
  flutter_test:
    sdk: flutter
  
  # Code Generation
  build_runner: ^2.4.6
  json_serializable: ^6.7.1
  retrofit_generator: ^8.0.4
  hive_generator: ^2.0.1
  
  # Linting
  flutter_lints: ^3.0.0

flutter:
  uses-material-design: true
  
  assets:
    - assets/images/
    - assets/icons/
    - assets/fonts/
  
  fonts:
    - family: Roboto
      fonts:
        - asset: assets/fonts/Roboto-Regular.ttf
        - asset: assets/fonts/Roboto-Bold.ttf
          weight: 700
```

### Configuração Android

```xml
<!-- android/app/src/main/AndroidManifest.xml -->
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
    
    <application
        android:label="CKO Framework"
        android:name="${applicationName}"
        android:icon="@mipmap/ic_launcher">
        
        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:launchMode="singleTop"
            android:theme="@style/LaunchTheme"
            android:configChanges="orientation|keyboardHidden|keyboard|screenSize|smallestScreenSize|locale|layoutDirection|fontScale|screenLayout|density|uiMode"
            android:hardwareAccelerated="true"
            android:windowSoftInputMode="adjustResize">
            
            <meta-data
                android:name="io.flutter.embedding.android.NormalTheme"
                android:resource="@style/NormalTheme" />
                
            <intent-filter android:autoVerify="true">
                <action android:name="android.intent.action.MAIN"/>
                <category android:name="android.intent.category.LAUNCHER"/>
            </intent-filter>
        </activity>
        
        <meta-data
            android:name="flutterEmbedding"
            android:value="2" />
    </application>
</manifest>
```

### Configuração iOS

```xml
<!-- ios/Runner/Info.plist -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>CFBundleDevelopmentRegion</key>
    <string>en</string>
    <key>CFBundleDisplayName</key>
    <string>CKO Framework</string>
    <key>CFBundleExecutable</key>
    <string>$(EXECUTABLE_NAME)</string>
    <key>CFBundleIdentifier</key>
    <string>$(PRODUCT_BUNDLE_IDENTIFIER)</string>
    <key>CFBundleInfoDictionaryVersion</key>
    <string>6.0</string>
    <key>CFBundleName</key>
    <string>$(PRODUCT_NAME)</string>
    <key>CFBundlePackageType</key>
    <string>APPL</string>
    <key>CFBundleShortVersionString</key>
    <string>$(FLUTTER_BUILD_NAME)</string>
    <key>CFBundleSignature</key>
    <string>????</string>
    <key>CFBundleVersion</key>
    <string>$(FLUTTER_BUILD_NUMBER)</string>
    <key>LSRequiresIPhoneOS</key>
    <true/>
    <key>UILaunchStoryboardName</key>
    <string>LaunchScreen</string>
    <key>UIMainStoryboardFile</key>
    <string>Main</string>
    <key>UISupportedInterfaceOrientations</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>UISupportedInterfaceOrientations~ipad</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationPortraitUpsideDown</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>UIViewControllerBasedStatusBarAppearance</key>
    <false/>
    <key>CADisableMinimumFrameDurationOnPhone</key>
    <true/>
    <key>UIApplicationSupportsIndirectInputEvents</key>
    <true/>
</dict>
</plist>
```

## 🎨 Componentes Principais

### Tela de Login

```dart
// lib/screens/auth/login_screen.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/common/loading_widget.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Logo
                Image.asset(
                  'assets/images/logo.png',
                  height: 100,
                ),
                SizedBox(height: 32),
                
                // Título
                Text(
                  'CKO Framework',
                  style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 8),
                
                Text(
                  'Gestão Financeira Inteligente',
                  style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: Colors.grey[600],
                  ),
                ),
                SizedBox(height: 48),
                
                // Email
                TextFormField(
                  controller: _emailController,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(
                    labelText: 'Email',
                    prefixIcon: Icon(Icons.email),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Email é obrigatório';
                    }
                    if (!value.contains('@')) {
                      return 'Email inválido';
                    }
                    return null;
                  },
                ),
                SizedBox(height: 16),
                
                // Senha
                TextFormField(
                  controller: _passwordController,
                  obscureText: true,
                  decoration: InputDecoration(
                    labelText: 'Senha',
                    prefixIcon: Icon(Icons.lock),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Senha é obrigatória';
                    }
                    if (value.length < 6) {
                      return 'Senha deve ter pelo menos 6 caracteres';
                    }
                    return null;
                  },
                ),
                SizedBox(height: 24),
                
                // Botão de Login
                Consumer<AuthProvider>(
                  builder: (context, authProvider, child) {
                    if (authProvider.isLoading) {
                      return LoadingWidget();
                    }
                    
                    return SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: ElevatedButton(
                        onPressed: _handleLogin,
                        child: Text('Entrar'),
                      ),
                    );
                  },
                ),
                SizedBox(height: 16),
                
                // Link para Registro
                TextButton(
                  onPressed: () {
                    Navigator.pushNamed(context, '/register');
                  },
                  child: Text('Não tem conta? Registre-se'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _handleLogin() async {
    if (_formKey.currentState!.validate()) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      
      try {
        await authProvider.login(
          _emailController.text,
          _passwordController.text,
        );
        
        Navigator.pushReplacementNamed(context, '/dashboard');
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erro ao fazer login: $e')),
        );
      }
    }
  }
}
```

### Dashboard Principal

```dart
// lib/screens/dashboard/dashboard_screen.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/finance_provider.dart';
import '../../widgets/finance/financial_card.dart';
import '../../widgets/finance/chart_widget.dart';

class DashboardScreen extends StatefulWidget {
  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  @override
  void initState() {
    super.initState();
    _loadData();
  }

  void _loadData() {
    final financeProvider = Provider.of<FinanceProvider>(context, listen: false);
    financeProvider.loadDashboardData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Dashboard'),
        actions: [
          IconButton(
            icon: Icon(Icons.refresh),
            onPressed: _loadData,
          ),
        ],
      ),
      body: Consumer<FinanceProvider>(
        builder: (context, financeProvider, child) {
          if (financeProvider.isLoading) {
            return Center(child: CircularProgressIndicator());
          }

          return RefreshIndicator(
            onRefresh: () async => _loadData(),
            child: SingleChildScrollView(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Cards de Resumo
                  Row(
                    children: [
                      Expanded(
                        child: FinancialCard(
                          title: 'Saldo Atual',
                          value: financeProvider.balance,
                          change: financeProvider.balanceChange,
                          icon: Icons.account_balance_wallet,
                          color: Colors.green,
                        ),
                      ),
                      SizedBox(width: 16),
                      Expanded(
                        child: FinancialCard(
                          title: 'Receitas',
                          value: financeProvider.income,
                          change: financeProvider.incomeChange,
                          icon: Icons.trending_up,
                          color: Colors.blue,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 16),
                  
                  Row(
                    children: [
                      Expanded(
                        child: FinancialCard(
                          title: 'Despesas',
                          value: financeProvider.expenses,
                          change: financeProvider.expensesChange,
                          icon: Icons.trending_down,
                          color: Colors.red,
                        ),
                      ),
                      SizedBox(width: 16),
                      Expanded(
                        child: FinancialCard(
                          title: 'Investimentos',
                          value: financeProvider.investments,
                          change: financeProvider.investmentsChange,
                          icon: Icons.show_chart,
                          color: Colors.purple,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 24),
                  
                  // Gráfico
                  Text(
                    'Evolução Financeira',
                    style: Theme.of(context).textTheme.headlineSmall,
                  ),
                  SizedBox(height: 16),
                  ChartWidget(
                    data: financeProvider.chartData,
                  ),
                  SizedBox(height: 24),
                  
                  // Ações Rápidas
                  Text(
                    'Ações Rápidas',
                    style: Theme.of(context).textTheme.headlineSmall,
                  ),
                  SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pushNamed(context, '/cashflow');
                          },
                          icon: Icon(Icons.account_balance),
                          label: Text('Fluxo de Caixa'),
                        ),
                      ),
                      SizedBox(width: 16),
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pushNamed(context, '/ai');
                          },
                          icon: Icon(Icons.psychology),
                          label: Text('AI Financeiro'),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
```

### Tela de Chat com AI

```dart
// lib/screens/ai/ai_chat_screen.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/ai_provider.dart';
import '../../widgets/ai/chat_bubble.dart';
import '../../widgets/ai/ai_status_widget.dart';

class AIChatScreen extends StatefulWidget {
  @override
  _AIChatScreenState createState() => _AIChatScreenState();
}

class _AIChatScreenState extends State<AIChatScreen> {
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('AI Financeiro'),
        actions: [
          AIStatusWidget(),
        ],
      ),
      body: Column(
        children: [
          // Lista de Mensagens
          Expanded(
            child: Consumer<AIProvider>(
              builder: (context, aiProvider, child) {
                return ListView.builder(
                  controller: _scrollController,
                  padding: EdgeInsets.all(16),
                  itemCount: aiProvider.messages.length,
                  itemBuilder: (context, index) {
                    final message = aiProvider.messages[index];
                    return ChatBubble(
                      message: message,
                      isUser: message.isUser,
                    );
                  },
                );
              },
            ),
          ),
          
          // Input de Mensagem
          Container(
            padding: EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Theme.of(context).cardColor,
              border: Border(
                top: BorderSide(color: Colors.grey[300]!),
              ),
            ),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'Digite sua pergunta...',
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(24),
                      ),
                      contentPadding: EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 12,
                      ),
                    ),
                    maxLines: null,
                    onSubmitted: _sendMessage,
                  ),
                ),
                SizedBox(width: 8),
                Consumer<AIProvider>(
                  builder: (context, aiProvider, child) {
                    return IconButton(
                      onPressed: aiProvider.isLoading ? null : _sendMessage,
                      icon: aiProvider.isLoading
                          ? SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            )
                          : Icon(Icons.send),
                    );
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _sendMessage() {
    final message = _messageController.text.trim();
    if (message.isNotEmpty) {
      final aiProvider = Provider.of<AIProvider>(context, listen: false);
      aiProvider.sendMessage(message);
      _messageController.clear();
      
      // Scroll para baixo
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (_scrollController.hasClients) {
          _scrollController.animateTo(
            _scrollController.position.maxScrollExtent,
            duration: Duration(milliseconds: 300),
            curve: Curves.easeOut,
          );
        }
      });
    }
  }
}
```

## 🔧 Serviços e APIs

### Serviço de API

```dart
// lib/services/api_service.dart
import 'package:dio/dio.dart';
import 'package:retrofit/retrofit.dart';
import '../models/user.dart';
import '../models/transaction.dart';
import '../models/trade.dart';
import '../models/holding.dart';
import '../models/ai_response.dart';

part 'api_service.g.dart';

@RestApi(baseUrl: "http://localhost:8000/api")
abstract class ApiService {
  factory ApiService(Dio dio, {String baseUrl}) = _ApiService;

  // Auth
  @POST("/auth/login")
  Future<Map<String, dynamic>> login(@Body() Map<String, dynamic> credentials);

  @POST("/auth/register")
  Future<Map<String, dynamic>> register(@Body() Map<String, dynamic> userData);

  @POST("/auth/refresh")
  Future<Map<String, dynamic>> refreshToken(@Body() Map<String, dynamic> tokenData);

  // Finance
  @GET("/cashflow")
  Future<List<Transaction>> getCashflow();

  @POST("/cashflow")
  Future<Transaction> createTransaction(@Body() Map<String, dynamic> transactionData);

  @GET("/trades")
  Future<List<Trade>> getTrades();

  @GET("/holdings")
  Future<List<Holding>> getHoldings();

  // AI
  @POST("/ai/analyze")
  Future<AIResponse> analyzeFinance(@Body() Map<String, dynamic> query);

  @GET("/ai/status")
  Future<Map<String, dynamic>> getAIStatus();
}
```

### Serviço de Autenticação

```dart
// lib/services/auth_service.dart
import 'package:shared_preferences/shared_preferences.dart';
import 'api_service.dart';

class AuthService {
  final ApiService _apiService;
  final SharedPreferences _prefs;

  AuthService(this._apiService, this._prefs);

  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await _apiService.login({
        'email': email,
        'password': password,
      });

      // Salvar token
      await _prefs.setString('token', response['token']);
      await _prefs.setString('user', response['user'].toString());

      return response;
    } catch (e) {
      throw Exception('Erro ao fazer login: $e');
    }
  }

  Future<void> logout() async {
    await _prefs.remove('token');
    await _prefs.remove('user');
  }

  String? getToken() {
    return _prefs.getString('token');
  }

  bool isLoggedIn() {
    return getToken() != null;
  }

  Future<Map<String, dynamic>?> getUser() async {
    final userString = _prefs.getString('user');
    if (userString != null) {
      // Parse user data
      return Map<String, dynamic>.from(userString);
    }
    return null;
  }
}
```

## 🎨 Temas e Estilos

### Tema Principal

```dart
// lib/themes/app_theme.dart
import 'package:flutter/material.dart';
import 'colors.dart';
import 'text_styles.dart';

class AppTheme {
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        brightness: Brightness.light,
      ),
      textTheme: AppTextStyles.textTheme,
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
      ),
      cardTheme: CardTheme(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    );
  }

  static ThemeData get darkTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        brightness: Brightness.dark,
      ),
      textTheme: AppTextStyles.textTheme,
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
      ),
      cardTheme: CardTheme(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    );
  }
}
```

### Cores

```dart
// lib/themes/colors.dart
import 'package:flutter/material.dart';

class AppColors {
  // Cores Primárias
  static const Color primary = Color(0xFF3B82F6);
  static const Color primaryDark = Color(0xFF1E40AF);
  static const Color primaryLight = Color(0xFF93C5FD);

  // Cores Secundárias
  static const Color secondary = Color(0xFF10B981);
  static const Color secondaryDark = Color(0xFF047857);
  static const Color secondaryLight = Color(0xFF6EE7B7);

  // Cores de Status
  static const Color success = Color(0xFF10B981);
  static const Color warning = Color(0xFFF59E0B);
  static const Color error = Color(0xFFEF4444);
  static const Color info = Color(0xFF3B82F6);

  // Cores Neutras
  static const Color background = Color(0xFFF9FAFB);
  static const Color surface = Color(0xFFFFFFFF);
  static const Color onSurface = Color(0xFF1F2937);
  static const Color onBackground = Color(0xFF374151);

  // Cores de Texto
  static const Color textPrimary = Color(0xFF111827);
  static const Color textSecondary = Color(0xFF6B7280);
  static const Color textDisabled = Color(0xFF9CA3AF);

  // Cores Financeiras
  static const Color income = Color(0xFF10B981);
  static const Color expense = Color(0xFFEF4444);
  static const Color investment = Color(0xFF8B5CF6);
  static const Color balance = Color(0xFF3B82F6);
}
```

## 🧪 Testes

### Testes de Widget

```dart
// test/widgets/financial_card_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:cko_framework_mobile/widgets/finance/financial_card.dart';

void main() {
  group('FinancialCard Widget Tests', () {
    testWidgets('should display title and value correctly', (WidgetTester tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: FinancialCard(
              title: 'Test Title',
              value: 'R\$ 1.000,00',
              change: '+10%',
              icon: Icons.account_balance_wallet,
              color: Colors.green,
            ),
          ),
        ),
      );

      expect(find.text('Test Title'), findsOneWidget);
      expect(find.text('R\$ 1.000,00'), findsOneWidget);
      expect(find.text('+10%'), findsOneWidget);
    });

    testWidgets('should display correct icon', (WidgetTester tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: FinancialCard(
              title: 'Test',
              value: 'R\$ 0,00',
              change: '0%',
              icon: Icons.account_balance_wallet,
              color: Colors.green,
            ),
          ),
        ),
      );

      expect(find.byIcon(Icons.account_balance_wallet), findsOneWidget);
    });
  });
}
```

### Testes de Integração

```dart
// integration_test/app_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:integration_test/integration_test.dart';
import 'package:cko_framework_mobile/main.dart' as app;

void main() {
  IntegrationTestWidgetsFlutterBinding.ensureInitialized();

  group('App Integration Tests', () {
    testWidgets('should navigate through app screens', (WidgetTester tester) async {
      app.main();
      await tester.pumpAndSettle();

      // Test login screen
      expect(find.text('CKO Framework'), findsOneWidget);
      
      // Test login flow
      await tester.enterText(find.byType(TextFormField).first, 'test@example.com');
      await tester.enterText(find.byType(TextFormField).last, 'password123');
      await tester.tap(find.text('Entrar'));
      await tester.pumpAndSettle();

      // Test dashboard
      expect(find.text('Dashboard'), findsOneWidget);
    });
  });
}
```

## 🚀 Build e Deploy

### Build para Android

```bash
# Debug
flutter build apk --debug

# Release
flutter build apk --release

# Bundle (Google Play)
flutter build appbundle --release
```

### Build para iOS

```bash
# Debug
flutter build ios --debug

# Release
flutter build ios --release

# Archive (App Store)
flutter build ipa --release
```

### Build para Web

```bash
# Debug
flutter build web --debug

# Release
flutter build web --release
```

### Deploy

```bash
# Android - Google Play
flutter build appbundle --release
# Upload bundle para Google Play Console

# iOS - App Store
flutter build ipa --release
# Upload via Xcode ou Transporter

# Web - Deploy
flutter build web --release
# Copiar build/web/ para servidor web
```

## 🔧 Troubleshooting

### Problemas Comuns

1. **Erro de Build**
   ```bash
   # Limpar cache
   flutter clean
   flutter pub get
   
   # Rebuild
   flutter build apk --release
   ```

2. **Erro de Dependências**
   ```bash
   # Atualizar dependências
   flutter pub upgrade
   
   # Verificar dependências
   flutter pub deps
   ```

3. **Erro de API**
   ```bash
   # Verificar conectividade
   flutter run --verbose
   
   # Testar API
   curl http://localhost:8000/api/health
   ```

### Comandos Úteis

```bash
# Verificar configuração
flutter doctor

# Limpar projeto
flutter clean

# Atualizar dependências
flutter pub upgrade

# Executar testes
flutter test

# Executar testes de integração
flutter test integration_test/

# Ver logs
flutter logs

# Hot reload
r (no terminal do Flutter)

# Hot restart
R (no terminal do Flutter)
```

---

**Documentação Flutter CKO Framework v1.0**
