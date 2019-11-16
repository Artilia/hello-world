
Partial Class response
    Inherits System.Web.UI.Page

    Protected Sub Page_Load(sender As Object, e As EventArgs) Handles Me.Load
        Dim dir As String = Server.MapPath(".")
        dir = dir & "/response.xml"
        dir = dir.Replace("\", "/")
        dir = dir.Replace("//", "/")


        Response.Clear()
        Response.ContentType = "text/xml"
        Response.ContentEncoding = Encoding.UTF8
        Response.Write(My.Computer.FileSystem.ReadAllText(dir))
        Response.End()
    End Sub
End Class
