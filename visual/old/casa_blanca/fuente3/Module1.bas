Attribute VB_Name = "funcione"
Dim Cadena As String
Public Cantidad_Paq As Integer
Public arr_colores(0 To 4)

Public control_arduino  As Boolean
Public control_arduino_t  As Boolean
Public cantidadtimercon As Integer
Public Cantidad_Paq_control As Integer

Public puerto As Integer
Public settings As String
Public config As String
Public con_db As String

Public url_get As String
Public contar_en As Integer

Function con_puerto()

On Error GoTo error

    control_arduino = True
    
    index.MSComm1.CommPort = puerto
    index.MSComm1.settings = settings
    index.MSComm1.InputLen = 0
    index.MSComm1.PortOpen = True
    
    index.but_con.Caption = "Desconectar"
    Cantidad_Paq = 0

Exit Function

error:

End Function

Function des_puerto()

On Error GoTo error

    index.MSComm1.PortOpen = False
    index.but_con.Caption = "Conectar"

Exit Function

error:

End Function

Function dat_puerto()

On Error GoTo error

    Dim i As Integer
    Dim Valor As String
    Dim arr_dat() As String
    
    Dim id_hab As Integer
    Dim est_hab As Integer
    
    Dim numero_a As Integer
    
    Valor = index.MSComm1.Input
    
    i = InStr(Valor, Chr(13))

    If i = 0 Then
        Cadena = Cadena & Valor
    Else
        Cadena = Cadena & Left(Valor, i - 1)
        
        If Cadena <> "start" Then
        
            Cantidad_Paq = Cantidad_Paq + 1
            
            ''If Cantidad_Paq = 10000 Then
            ''If Cantidad_Paq = 60 Then
                ''Call des_puerto
                ''Call con_puerto
            ''End If
            
            arr_dat = Split(Cadena, ";")
            For i = 0 To UBound(arr_dat)
            
                If i = 0 Then
                    id_hab = Val(arr_dat(i))
                Else
                    est_hab = Val(arr_dat(i))
                End If
                
            Next
            
            index.hab(id_hab).BackColor = arr_colores(est_hab)

            contar_en = contar_en + 1
            
            If contar_en = 8 Then
                contar_en = 0
                
                numero_a = Aleatorio(1, 9999)
                
                url_get = url_get & id_hab & "-" & est_hab
                
                ''url_get = "http://192.168.7.118:81/ard/casa_blanca/json.php?n=" & numero_a & "&hab=" & url_get
                url_get = "http://localhost/json.php?n=" & numero_a & "&hab=" & url_get
                
                index.WebBrowser1.Navigate url_get
                
                url_get = ""
                
            Else
            
                url_get = url_get & id_hab & "-" & est_hab & "~"
            
            End If

            index.txt_env_get.Text = contar_en
            
            
            
                        
            index.txt_can_paq.Text = Cantidad_Paq
            
            index.txt_dato.Text = Cadena
        
        End If
    
        Cadena = ""
    End If

Exit Function

error:
    Call des_puerto
    Call con_puerto

End Function

Function Aleatorio(Minimo As Long, Maximo As Long) As Long
    Randomize ' inicializar la semilla
    Aleatorio = CLng((Minimo - Maximo) * Rnd + Maximo)
End Function
