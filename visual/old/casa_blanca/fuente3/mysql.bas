Attribute VB_Name = "mysql"
Public cn As ADODB.Connection
Public rsdyn As Recordset
Public rsdyn2 As Recordset

Sub abrir()

On Error GoTo error
    
    Set cn = New ADODB.Connection
    cn.Open (con_db)

Exit Sub

error:
    MsgBox ("No hay conexion con la base de datos: " & Err.Description)

End Sub

Sub query(sql1 As String)

    Set rsdyn = New Recordset
    rsdyn.CursorLocation = adUseClient
    rsdyn.Open sql1, cn, adLockOptimistic, adLockOptimistic
    
    ''rsdyn.RecordCount
    
'    query ("SELECT * FROM hab")
'    If rsdyn.RecordCount <> 0 Then
'        While rsdyn.EOF = False
'
'            MsgBox rsdyn.Fields("id_hab")
'
'            rsdyn.MoveNext
'
'        Wend
'    End If

End Sub


