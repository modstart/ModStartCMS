@extends('modstart::admin.frame')

@section('headAppend')
    @parent
    <style type="text/css">
        body{
            min-height:100vh;
            background-color:#F7F7F7;
            background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjEzNjFweCIgaGVpZ2h0PSI2MDlweCIgdmlld0JveD0iMCAwIDEzNjEgNjA5IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBDb3B5cmlnaHQgaHR0cHM6Ly9tb2RzdGFydC5jb20gLS0+CiAgICA8dGl0bGU+TW9kU3RhcnQ8L3RpdGxlPgogICAgPGRlc2M+TG9naW5CZzwvZGVzYz4KICAgIDxkZWZzPjwvZGVmcz4KICAgIDxnIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKC03OS4wMDAwMDAsIC04Mi4wMDAwMDApIj4KICAgICAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzcuMDAwMDAwLCA3My4wMDAwMDApIj4KICAgICAgICAgICAgICAgIDxnIG9wYWNpdHk9IjAuOCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzQuOTAxNDE2LCA1NjkuNjk5MTU4KSByb3RhdGUoLTcuMDAwMDAwKSB0cmFuc2xhdGUoLTc0LjkwMTQxNiwgLTU2OS42OTkxNTgpIHRyYW5zbGF0ZSg0LjkwMTQxNiwgNTI1LjE5OTE1OCkiPgogICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGZpbGw9IiNDRkRBRTYiIG9wYWNpdHk9IjAuMjUiIGN4PSI2My41NzQ4NzkyIiBjeT0iMzIuNDY4MzY3IiByeD0iMjEuNzgzMDQ3OSIgcnk9IjIxLjc2NjAwOCI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGZpbGw9IiNDRkRBRTYiIG9wYWNpdHk9IjAuNTk5OTk5OTY0IiBjeD0iNS45ODc0NjQ3OSIgY3k9IjEzLjg2Njg2MDEiIHJ4PSI1LjIxNzM5MTMiIHJ5PSI1LjIxMzMwOTk3Ij48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTM4LjEzNTQ1MTQsODguMzUyMDIxNSBDNDMuODk4NDIyNyw4OC4zNTIwMjE1IDQ4LjU3MDIzNCw4My42ODM4NjQ3IDQ4LjU3MDIzNCw3Ny45MjU0MDE1IEM0OC41NzAyMzQsNzIuMTY2OTM4MyA0My44OTg0MjI3LDY3LjQ5ODc4MTYgMzguMTM1NDUxNCw2Ny40OTg3ODE2IEMzMi4zNzI0ODAxLDY3LjQ5ODc4MTYgMjcuNzAwNjY4OCw3Mi4xNjY5MzgzIDI3LjcwMDY2ODgsNzcuOTI1NDAxNSBDMjcuNzAwNjY4OCw4My42ODM4NjQ3IDMyLjM3MjQ4MDEsODguMzUyMDIxNSAzOC4xMzU0NTE0LDg4LjM1MjAyMTUgWiIgZmlsbD0iI0NGREFFNiIgb3BhY2l0eT0iMC40NSI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik02NC4yNzc1NTgyLDMzLjE3MDQ5NjMgTDExOS4xODU4MzYsMTYuNTY1NDkxNSIgc3Ryb2tlPSIjQ0ZEQUU2IiBzdHJva2Utd2lkdGg9IjEuNzM5MTMwNDMiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik00Mi4xNDMxNzA4LDI2LjUwMDI2ODEgTDcuNzExOTAxNjIsMTQuNTY0MDcwMiIgc3Ryb2tlPSIjRTBCNEI3IiBzdHJva2Utd2lkdGg9IjAuNzAyNjc4OTY0IiBvcGFjaXR5PSIwLjciIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLWRhc2hhcnJheT0iMS40MDUzNTc4OTk4NzMxNTMsMi4xMDgwMzY5NTM0Njk5ODEiPjwvcGF0aD4KICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNjMuOTI2MjE4NywzMy41MjE1NjEgTDQzLjY3MjEzMjYsNjkuMzI1MDk1MSIgc3Ryb2tlPSIjQkFDQUQ5IiBzdHJva2Utd2lkdGg9IjAuNzAyNjc4OTY0IiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIHN0cm9rZS1kYXNoYXJyYXk9IjEuNDA1MzU3ODk5ODczMTUzLDIuMTA4MDM2OTUzNDY5OTgxIj48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTI2Ljg1MDkyMiwgMTMuNTQzNjU0KSByb3RhdGUoMzAuMDAwMDAwKSB0cmFuc2xhdGUoLTEyNi44NTA5MjIsIC0xMy41NDM2NTQpIHRyYW5zbGF0ZSgxMTcuMjg1NzA1LCA0LjM4MTg4OSkiIGZpbGw9IiNDRkRBRTYiPgogICAgICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBvcGFjaXR5PSIwLjQ1IiBjeD0iOS4xMzQ4MjY1MyIgY3k9IjkuMTI3NjgwNzYiIHJ4PSI5LjEzNDgyNjUzIiByeT0iOS4xMjc2ODA3NiI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTguMjY5NjUzMSwxOC4yNTUzNjE1IEMxOC4yNjk2NTMxLDEzLjIxNDI4MjYgMTQuMTc5ODUxOSw5LjEyNzY4MDc2IDkuMTM0ODI2NTMsOS4xMjc2ODA3NiBDNC4wODk4MDExNCw5LjEyNzY4MDc2IDAsMTMuMjE0MjgyNiAwLDE4LjI1NTM2MTUgTDE4LjI2OTY1MzEsMTguMjU1MzYxNSBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg5LjEzNDgyNywgMTMuNjkxNTIxKSBzY2FsZSgtMSwgLTEpIHRyYW5zbGF0ZSgtOS4xMzQ4MjcsIC0xMy42OTE1MjEpICI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIxNi4yOTQ3MDAsIDEyMy43MjU2MDApIHJvdGF0ZSgtNS4wMDAwMDApIHRyYW5zbGF0ZSgtMjE2LjI5NDcwMCwgLTEyMy43MjU2MDApIHRyYW5zbGF0ZSgxMDYuMjk0NzAwLCAzNS4yMjU2MDApIj4KICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBmaWxsPSIjQ0ZEQUU2IiBvcGFjaXR5PSIwLjI1IiBjeD0iMjkuMTE3NjQ3MSIgY3k9IjI5LjE0MDI0MzkiIHJ4PSIyOS4xMTc2NDcxIiByeT0iMjkuMTQwMjQzOSI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGZpbGw9IiNDRkRBRTYiIG9wYWNpdHk9IjAuMyIgY3g9IjI5LjExNzY0NzEiIGN5PSIyOS4xNDAyNDM5IiByeD0iMjEuNTY4NjI3NSIgcnk9IjIxLjU4NTM2NTkiPjwvZWxsaXBzZT4KICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBzdHJva2U9IiNDRkRBRTYiIG9wYWNpdHk9IjAuNCIgY3g9IjE3OS4wMTk2MDgiIGN5PSIxMzguMTQ2MzQxIiByeD0iMjMuNzI1NDkwMiIgcnk9IjIzLjc0MzkwMjQiPjwvZWxsaXBzZT4KICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBmaWxsPSIjQkFDQUQ5IiBvcGFjaXR5PSIwLjUiIGN4PSIyOS4xMTc2NDcxIiBjeT0iMjkuMTQwMjQzOSIgcng9IjEwLjc4NDMxMzciIHJ5PSIxMC43OTI2ODI5Ij48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTI5LjExNzY0NzEsMzkuOTMyOTI2OCBMMjkuMTE3NjQ3MSwxOC4zNDc1NjEgQzIzLjE2MTYzNTEsMTguMzQ3NTYxIDE4LjMzMzMzMzMsMjMuMTc5NjA5NyAxOC4zMzMzMzMzLDI5LjE0MDI0MzkgQzE4LjMzMzMzMzMsMzUuMTAwODc4MSAyMy4xNjE2MzUxLDM5LjkzMjkyNjggMjkuMTE3NjQ3MSwzOS45MzI5MjY4IFoiIGZpbGw9IiNCQUNBRDkiPjwvcGF0aD4KICAgICAgICAgICAgICAgICAgICA8ZyBvcGFjaXR5PSIwLjQ1IiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxNzIuMDAwMDAwLCAxMzEuMDAwMDAwKSIgZmlsbD0iI0U2QTFBNiI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGN4PSI3LjAxOTYwNzg0IiBjeT0iNy4xNDYzNDE0NiIgcng9IjYuNDcwNTg4MjQiIHJ5PSI2LjQ3NTYwOTc2Ij48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0wLjU0OTAxOTYwOCwxMy42MjE5NTEyIEM0LjEyMjYyNjgxLDEzLjYyMTk1MTIgNy4wMTk2MDc4NCwxMC43MjI3MjIgNy4wMTk2MDc4NCw3LjE0NjM0MTQ2IEM3LjAxOTYwNzg0LDMuNTY5OTYwOTUgNC4xMjI2MjY4MSwwLjY3MDczMTcwNyAwLjU0OTAxOTYwOCwwLjY3MDczMTcwNyBMMC41NDkwMTk2MDgsMTMuNjIxOTUxMiBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzLjc4NDMxNCwgNy4xNDYzNDEpIHNjYWxlKC0xLCAxKSB0cmFuc2xhdGUoLTMuNzg0MzE0LCAtNy4xNDYzNDEpICI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBmaWxsPSIjQ0ZEQUU2IiBjeD0iMjE4LjM4MjM1MyIgY3k9IjEzOC42ODU5NzYiIHJ4PSIxLjYxNzY0NzA2IiByeT0iMS42MTg5MDI0NCI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGZpbGw9IiNFMEI0QjciIG9wYWNpdHk9IjAuMzUiIGN4PSIxNzkuNTU4ODI0IiBjeT0iMTc1LjM4MTA5OCIgcng9IjEuNjE3NjQ3MDYiIHJ5PSIxLjYxODkwMjQ0Ij48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgPGVsbGlwc2UgZmlsbD0iI0UwQjRCNyIgb3BhY2l0eT0iMC4zNSIgY3g9IjE4MC4wOTgwMzkiIGN5PSIxMDIuNTMwNDg4IiByeD0iMi4xNTY4NjI3NSIgcnk9IjIuMTU4NTM2NTkiPjwvZWxsaXBzZT4KICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjguOTk4NTM4MSwyOS45NjcxNTk4IEwxNzEuMTUxMDE4LDEzMi44NzYwMjQiIHN0cm9rZT0iI0NGREFFNiIgb3BhY2l0eT0iMC44Ij48L3BhdGg+CiAgICAgICAgICAgICAgICA8L2c+CiAgICAgICAgICAgICAgICA8ZyBvcGFjaXR5PSIwLjc5OTk5OTk1MiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTA1NC4xMDA2MzUsIDM2LjY1OTMxNykgcm90YXRlKC0xMS4wMDAwMDApIHRyYW5zbGF0ZSgtMTA1NC4xMDA2MzUsIC0zNi42NTkzMTcpIHRyYW5zbGF0ZSgxMDI2LjYwMDYzNSwgNC42NTkzMTcpIj4KICAgICAgICAgICAgICAgICAgICA8ZWxsaXBzZSBzdHJva2U9IiNDRkRBRTYiIHN0cm9rZS13aWR0aD0iMC45NDExNzY0NzEiIGN4PSI0My44MTM1NTkzIiBjeT0iMzIiIHJ4PSIxMS4xODY0NDA3IiByeT0iMTEuMjk0MTE3NiI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDM0LjU5Njc3NCwgMjMuMTExMTExKSIgZmlsbD0iI0JBQ0FEOSI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIG9wYWNpdHk9IjAuNDUiIGN4PSI5LjE4NTM0NzE4IiBjeT0iOC44ODg4ODg4OSIgcng9IjguNDc0NTc2MjciIHJ5PSI4LjU1NjE0OTczIj48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik05LjE4NTM0NzE4LDE3LjQ0NTAzODYgQzEzLjg2NTcyNjQsMTcuNDQ1MDM4NiAxNy42NTk5MjM1LDEzLjYxNDMxOTkgMTcuNjU5OTIzNSw4Ljg4ODg4ODg5IEMxNy42NTk5MjM1LDQuMTYzNDU3ODcgMTMuODY1NzI2NCwwLjMzMjczOTE1NiA5LjE4NTM0NzE4LDAuMzMyNzM5MTU2IEw5LjE4NTM0NzE4LDE3LjQ0NTAzODYgWiIgaWQ9Ik92YWwtNyI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMzQuNjU5NzM4NSwyNC44MDk2OTQgTDUuNzE2NjYwODQsNC43Njg3ODk0NSIgc3Ryb2tlPSIjQ0ZEQUU2IiBzdHJva2Utd2lkdGg9IjAuOTQxMTc2NDcxIj48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPGVsbGlwc2Ugc3Ryb2tlPSIjQ0ZEQUU2IiBzdHJva2Utd2lkdGg9IjAuOTQxMTc2NDcxIiBjeD0iMy4yNjI3MTE4NiIgY3k9IjMuMjk0MTE3NjUiIHJ4PSIzLjI2MjcxMTg2IiByeT0iMy4yOTQxMTc2NSI+PC9lbGxpcHNlPgogICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGZpbGw9IiNGN0UxQUQiIGN4PSIyLjc5NjYxMDE3IiBjeT0iNjEuMTc2NDcwNiIgcng9IjIuNzk2NjEwMTciIHJ5PSIyLjgyMzUyOTQxIj48L2VsbGlwc2U+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTM0LjYzMTI0NDMsMzkuMjkyMjcxMiBMNS4wNjM2NjY2Myw1OS43ODUwODIiIHN0cm9rZT0iI0NGREFFNiIgc3Ryb2tlLXdpZHRoPSIwLjk0MTE3NjQ3MSI+PC9wYXRoPgogICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICAgICAgPGcgb3BhY2l0eT0iMC4zMyIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTI4Mi41MzcyMTksIDQ0Ni41MDI4NjcpIHJvdGF0ZSgtMTAuMDAwMDAwKSB0cmFuc2xhdGUoLTEyODIuNTM3MjE5LCAtNDQ2LjUwMjg2NykgdHJhbnNsYXRlKDExNDIuNTM3MjE5LCAzMjcuNTAyODY3KSI+CiAgICAgICAgICAgICAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTQxLjMzMzUzOSwgMTA0LjUwMjc0Mikgcm90YXRlKDI3NS4wMDAwMDApIHRyYW5zbGF0ZSgtMTQxLjMzMzUzOSwgLTEwNC41MDI3NDIpIHRyYW5zbGF0ZSgxMjkuMzMzNTM5LCA5Mi41MDI3NDIpIiBmaWxsPSIjQkFDQUQ5Ij4KICAgICAgICAgICAgICAgICAgICAgICAgPGNpcmNsZSBvcGFjaXR5PSIwLjQ1IiBjeD0iMTEuNjY2NjY2NyIgY3k9IjExLjY2NjY2NjciIHI9IjExLjY2NjY2NjciPjwvY2lyY2xlPgogICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjMuMzMzMzMzMywyMy4zMzMzMzMzIEMyMy4zMzMzMzMzLDE2Ljg5MDAxMTMgMTguMTA5OTg4NywxMS42NjY2NjY3IDExLjY2NjY2NjcsMTEuNjY2NjY2NyBDNS4yMjMzNDQ1OSwxMS42NjY2NjY3IDAsMTYuODkwMDExMyAwLDIzLjMzMzMzMzMgTDIzLjMzMzMzMzMsMjMuMzMzMzMzMyBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMS42NjY2NjcsIDE3LjUwMDAwMCkgc2NhbGUoLTEsIC0xKSB0cmFuc2xhdGUoLTExLjY2NjY2NywgLTE3LjUwMDAwMCkgIj48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICAgICAgICAgIDxjaXJjbGUgZmlsbD0iI0NGREFFNiIgY3g9IjIwMS44MzMzMzMiIGN5PSI4Ny41IiByPSI1LjgzMzMzMzMzIj48L2NpcmNsZT4KICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTQzLjUsODguODEyNjY4NSBMMTU1LjA3MDUwMSwxNy42MDM4NTQ0IiBzdHJva2U9IiNCQUNBRDkiIHN0cm9rZS13aWR0aD0iMS4xNjY2NjY2NyI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0xNy41LDM3LjMzMzMzMzMgTDEyNy40NjYyNTIsOTcuNjQ0OTczNSIgc3Ryb2tlPSIjQkFDQUQ5IiBzdHJva2Utd2lkdGg9IjEuMTY2NjY2NjciPjwvcGF0aD4KICAgICAgICAgICAgICAgICAgICA8cG9seWxpbmUgc3Ryb2tlPSIjQ0ZEQUU2IiBzdHJva2Utd2lkdGg9IjEuMTY2NjY2NjciIHBvaW50cz0iMTQzLjkwMjU5NyAxMjAuMzAyMjgxIDE3NC45MzU0NTUgMjMxLjU3MTM0MiAzOC41IDE0Ny41MTA4NDcgMTI2LjM2Njk0MSAxMTAuODMzMzMzIj48L3BvbHlsaW5lPgogICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0xNTkuODMzMzMzLDk5Ljc0NTM4NDIgTDE5NS40MTY2NjcsODkuMjUiIHN0cm9rZT0iI0UwQjRCNyIgc3Ryb2tlLXdpZHRoPSIxLjE2NjY2NjY3IiBvcGFjaXR5PSIwLjYiPjwvcGF0aD4KICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjA1LjMzMzMzMyw4Mi4xMzcyMTA1IEwyMzguNzE5NDA2LDM2LjE2NjY2NjciIHN0cm9rZT0iI0JBQ0FEOSIgc3Ryb2tlLXdpZHRoPSIxLjE2NjY2NjY3Ij48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTI2Ni43MjM0MjQsMTMyLjIzMTk4OCBMMjA3LjA4MzMzMyw5MC40MTY2NjY3IiBzdHJva2U9IiNDRkRBRTYiIHN0cm9rZS13aWR0aD0iMS4xNjY2NjY2NyI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDxjaXJjbGUgZmlsbD0iI0MxRDFFMCIgY3g9IjE1Ni45MTY2NjciIGN5PSI4Ljc1IiByPSI4Ljc1Ij48L2NpcmNsZT4KICAgICAgICAgICAgICAgICAgICA8Y2lyY2xlIGZpbGw9IiNDMUQxRTAiIGN4PSIzOS4wODMzMzMzIiBjeT0iMTQ4Ljc1IiByPSI1LjI1Ij48L2NpcmNsZT4KICAgICAgICAgICAgICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iMC42IiBmaWxsPSIjRDFERUVEIiBjeD0iOC43NSIgY3k9IjMzLjI1IiByPSI4Ljc1Ij48L2NpcmNsZT4KICAgICAgICAgICAgICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iMC42IiBmaWxsPSIjRDFERUVEIiBjeD0iMjQzLjgzMzMzMyIgY3k9IjMwLjMzMzMzMzMiIHI9IjUuODMzMzMzMzMiPjwvY2lyY2xlPgogICAgICAgICAgICAgICAgICAgIDxjaXJjbGUgZmlsbD0iI0UwQjRCNyIgY3g9IjE3NS41ODMzMzMiIGN5PSIyMzIuNzUiIHI9IjUuMjUiPjwvY2lyY2xlPgogICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8L2c+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4=);
            background-repeat: no-repeat;
            background-position: 100%;
            background-size: 100%;
        }
    </style>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('AdminLoginHeadAppend'); !!}
@endsection

@section('bodyAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('AdminLoginBodyAppend'); !!}
@endsection

@section('body')
    <div class="ub-admin-login">
        <div class="login-box">
            <div class="info">
                <div class="title">
                    <i class="iconfont icon-user-o"></i> {!! L('Admin Login') !!}
                </div>
                <div class="slogan">
                    @if(modstart_config('adminLoginSlogan'))
                        {{ modstart_config('adminLoginSlogan') }}
                    @elseif(defined('\App\Constant\AppConstant::APP_NAME'))
                        {{\App\Constant\AppConstant::APP_NAME}}
                    @else
                        Admin Login
                    @endif
                </div>
            </div>
            <div class="form">
                <form class="ub-form" method="post" action="?" data-ajax-form>
                    @if(config('modstart.admin.login.captcha',false) && $captchaProvider && $captchaProvider->name()=='sms' && modstart_config('AdminManagerEnhance_SmsCaptchaQuick',false))
                        {{--Ignore Username and password--}}
                    @else
                        <div class="line">
                            <i class="iconfont icon-user"></i>
                            {{ L('Username') }}
                            <input type="text" name="username" value="{{\Illuminate\Support\Facades\Input::get('username','')}}" placeholder="{{ L('Please Input') }}"/>
                        </div>
                        <div class="line">
                            <i class="iconfont icon-lock"></i>
                            {{ L('Password') }}
                            <input type="password" name="password" value="{{\Illuminate\Support\Facades\Input::get('password','')}}" placeholder="{{ L('Please Input') }}"/>
                        </div>
                    @endif
                    @if(config('modstart.admin.login.captcha',false))
                        @if($captchaProvider)
                            <div style="padding:0.5rem;">
                                {!! $captchaProvider->render() !!}
                            </div>
                        @else
                            <div class="line">
                                <i class="iconfont icon-robot"></i>
                                {{ L('Captcha') }}
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="captcha" value="" autocomplete="off" placeholder="{{ L('Please Input') }}"/>
                                    </div>
                                    <div class="col-6">
                                        <img data-captcha style="height:40px;width:100%;border:1px solid #CCC;border-radius:3px;" data-uk-tooltip title="{{ L('Click To Refresh') }}" src="{{action('\ModStart\Admin\Controller\AuthController@loginCaptcha')}}?{{time()}}" onclick="this.src='{{action('\ModStart\Admin\Controller\AuthController@loginCaptcha')}}?'+Math.random();" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="line">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(\Illuminate\Support\Facades\Input::get('redirect',config('env.ADMIN_PATH','/admin/'))); ?>">
                        <button type="submit" class="btn btn-block btn-lg btn-primary">
                            {{ L('Submit') }}
                            <i class="iconfont icon-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
